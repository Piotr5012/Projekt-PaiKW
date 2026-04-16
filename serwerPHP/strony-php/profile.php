<?php
session_start();
require_once '../skrypty-php/database.php';

$db = new Database();

$profileId = $_GET['id'] ?? ($_SESSION['user_id'] ?? null);

if (!$profileId) {
    header('Location: ../index.php');
    exit();
}

$user = $db->getUserById($profileId);

if (!$user) {
    die("Użytkownik o podanym ID nie istnieje.");
}

$posts = $db->getPostsByUserId($profileId);

$isOwnProfile = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $profileId);

$followersCount = $db->getFollowersCount($profileId);
$isFollowing = false;

if (isset($_SESSION['user_id']) && !$isOwnProfile) {
    $isFollowing = $db->isFollowing($_SESSION['user_id'], $profileId);
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?= htmlspecialchars($user['username']) ?></title>
    <link rel="stylesheet" href="../inne/main_style.css">
    <link rel="stylesheet" href="../inne/profil_style.css">

    <script src="../inne/profileshow.js" defer></script>
    <script src="../inne/script.js"></script>
</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <a href="main.php" class="back-link-nav">← Menu główne</a>
            <a href="javascript:void(0)" 
                onclick="smartBack('<?= isset($_SESSION['last_list_page']) ? $_SESSION['last_list_page'] : 'posts_view.php' ?>')" 
                class="back-link-nav">
                ← Powrót
            </a>
        </div>

        <div class="nav-center">
            <a href="create_post.php">
                <button type="button" class="epicbtn">Dodaj post</button>
            </a>
        </div>

        <div class="nav-content">
            <?php if (isset($_SESSION['logged_in'])): ?>
                <a href="../skrypty-php/logout.php" class="logout-link">Wyloguj się</a>
            <?php endif; ?>
        </div>
    </nav>


    <div class="message-container" style="text-align: center; margin-top: 20px;">
        <?php if (isset($_SESSION['success_post'])): ?>
            <span class="text-sm text-success" style="color: #28a745; font-weight: bold;">
                <?= $_SESSION['success_post'] ?>
            </span>
            <?php unset($_SESSION['success_post']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_edit'])): ?>
            <span class="text-success"><?= $_SESSION['success_edit'] ?></span>
            <?php unset($_SESSION['success_edit']); ?>
        <?php endif; ?>
    </div>

    <div class="profile-layout">
        
        <div class="profile-options">
            <button type="button" class="settings-btn" aria-label="Opcje profilu">
                <span class="icon-dots"></span>
            </button>
            <div class="options-menu">
                <?php if ($isOwnProfile): ?>
                    <a href="edit_profile.php">Edytuj profil</a>
                <?php endif; ?>
                <button type="button" onclick="toggleProfileDetails()" id="toggleBtn">Szczegółowe informacje</button>
            </div>
        </div>

        <div class="profile-top-info">
            <div class="profile-avatar-container">
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="profile-page-avatar">
            </div>

            <div class="profile-details" id="profileContent">
                <div id="view-default">
                    <h1 class="profile-username">
                        <?= htmlspecialchars($user['username']) ?>
                        
                        <span class="followers-count">
                            Obserwujący: <strong><?= $followersCount ?></strong>
                        </span>

                        <?php if (isset($_SESSION['user_id']) && !$isOwnProfile): ?>
                            <a href="../skrypty-php/follow.php?id=<?= $profileId ?>" 
                            class="follow-btn <?= $isFollowing ? 'following' : '' ?>">
                                <?= $isFollowing ? '✓ Obserwujesz' : '+ Obserwuj' ?>
                            </a>
                        <?php endif; ?>
                    </h1>

                    <div class="profile-bio-box">
                        <?php if (!empty($user['bio'])): ?>
                            <?= nl2br(htmlspecialchars($user['bio'])) ?>
                        <?php else: ?>
                            <span style="opacity: 0.5;">Użytkownik nie dodał jeszcze opisu.</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="view-details" style="display: none;">
                    <h1 class="profile-username">Szczegóły profilu</h1>
                    <div class="profile-bio-box">
                        <p><strong>E-mail:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <p><strong>Dołączył:</strong> <?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <div class="posts-outside-container">
        <h3 class="section-title">Ostatnie posty użytkownika:</h3>
        <div class="user-activity-list">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <?php if ($isOwnProfile): ?>
                            <div class="post-top-actions">
                                <a href="edit_post.php?id=<?= $post['id'] ?>" class="post-action-btn edit" title="Edytuj">
                                    <span class="icon-edit"></span>
                                </a>
                                <a href="../skrypty-php/delete_post.php?id=<?= $post['id'] ?>" 
                                class="post-action-btn delete" 
                                onclick="return confirm('Usunąć post?')">
                                    <span class="icon-delete"></span>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($post['image'])): ?>
                            <div class="post-image">
                                <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post image">
                            </div>
                        <?php endif; ?>

                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="post.php?id=<?= $post['id'] ?>" class="post-link">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h2>

                            <div class="post-text">
                                <?= nl2br(htmlspecialchars($post['content'])) ?>
                            </div>

                            <div class="post-footer">
                                <div class="post-stats-mini">
                                    <div class="stat-item"><span class="icon-mini icon-like"></span> <?= $post['likes_count'] ?></div>
                                    <div class="stat-item"><span class="icon-mini icon-dislike"></span> <?= $post['dislikes_count'] ?></div>
                                    <div class="stat-item"><span class="icon-mini icon-comment"></span> <?= $post['comments_count'] ?></div>
                                </div>
                                <div class="footer-right">
                                    <span class="category-tag"><?= htmlspecialchars($post['category_name']) ?></span>
                                    <span class="author"><?= date('d.m.Y', strtotime($post['data'])) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #666; text-align: center; padding: 40px;">Użytkownik nie dodał jeszcze żadnych postów.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>