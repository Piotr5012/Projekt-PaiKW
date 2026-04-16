<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../skrypty-php/database.php';
$db = new Database();
$current_user_id = $_SESSION['user_id'];

$search_user_query = $_GET['search_user'] ?? '';
$search_results = [];
if (!empty($search_user_query)) {
    $search_results = $db->searchUsers($search_user_query);
}

$posts = $db->getFollowedPosts($current_user_id);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obserwowani</title>
    <link rel="stylesheet" href="../inne/main_style.css">
    <link rel="stylesheet" href="../inne/posts_view.css">
    <link rel="stylesheet" href="../inne/profil_style.css">
    <link rel="stylesheet" href="../inne/followers_style.css"> 
</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <a href="../strony-php/main.php" class="back-link-nav">← Menu główne</a>
        </div>

        <div class="nav-center">
            <a href="../strony-php/create_post.php">
                <button type="button" class="epicbtn">Dodaj post</button>
            </a>
        </div>

        <div class="nav-content">
            <?php if (isset($_SESSION['logged_in'])): ?>
                <a href="../skrypty-php/logout.php" class="logout-link">Wyloguj się</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="main-layout">
        
        <aside class="sidebar-filters">
            <form action="" method="GET">
                <h3 class="filter-title">Znajdź profil</h3>
                <div class="filter-list filter-list--search">
                    <input type="text" name="search_user" placeholder="Wpisz nick..."
                        value="<?= htmlspecialchars($search_user_query) ?>"
                        class="filter-input">
                </div>
                <button type="submit" class="follow-btn sidebar-search-btn" style="width: 100%; justify-content: center; margin-top: 10px;">
                    Szukaj
                </button>
            </form>

            <?php if (!empty($search_results)): ?>
                <div class="user-search-results">
                    <h4>Wyniki wyszukiwania:</h4>
                    <?php foreach ($search_results as $u): ?>
                        <div class="user-row">
                            <a href="profile.php?id=<?= $u['id'] ?>" class="user-info-mini">
                                <?php $searchAvatar = !empty($u['avatar']) ? $u['avatar'] : '../avatars/defoult_avatar/user.png'; ?>
                                <img src="<?= htmlspecialchars($searchAvatar) ?>" class="user-avatar-small" alt="avatar">
                                <span><?= htmlspecialchars($u['username']) ?></span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </aside>

        <div class="overview-container">
            <div class="feed-header">
                <h2 class="feed-title">Posty osób które obserwujesz:</h2>
            </div>
            
            <?php if (empty($posts)): ?>
                <div class="no-posts-container">
                    <p>Nie obserwujesz jeszcze nikogo lub Twoi znajomi nie dodali jeszcze żadnych postów.</p>
                    <p style="font-size: 0.8em; margin-top: 10px;">Użyj wyszukiwarki po lewej, aby znaleźć osoby do obserwowania!</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <?php if (!empty($post['image'])): ?>
                            <div class="post-image">
                                <img src="<?= htmlspecialchars($post['image']) ?>" alt="Zdjęcie posta">
                            </div>
                        <?php endif; ?>

                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="post.php?id=<?= $post['id'] ?>" class="post-link">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h2>
                            
                            <p class="post-text">
                                <?= nl2br(htmlspecialchars(mb_strimwidth($post['content'], 0, 250, "..."))) ?>
                            </p>
                            
                            <div class="post-footer">
                                <div class="footer-left">
                                    <span class="category-tag"><?= htmlspecialchars($post['category_name']) ?></span>
                                </div>

                                <div class="footer-right">
                                    <div class="post-stats-mini">
                                        <div class="stat-item" title="Polubienia">
                                            <span class="icon-mini icon-like"></span>
                                            <span class="stat-count"><?= $post['likes_count'] ?? 0 ?></span>
                                        </div>
                                        <div class="stat-item" title="Nie lubię">
                                            <span class="icon-mini icon-dislike"></span>
                                            <span class="stat-count"><?= $post['dislikes_count'] ?? 0 ?></span>
                                        </div>
                                        <div class="stat-item" title="Komentarze">
                                            <span class="icon-mini icon-comment"></span>
                                            <span class="stat-count"><?= $post['comments_count'] ?? 0 ?></span>
                                        </div>
                                    </div>
                                    
                                    <span class="post-author-info">
                                        Autor: 
                                        <a href="profile.php?id=<?= (int)$post['author_id'] ?>" class="author-profile-link">
                                            <?php 
                                                $avatarPath = !empty($post['avatar']) ? $post['avatar'] : '../avatars/defoult_avatar/user.png';
                                            ?>
                                            <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Avatar" class="author-mini-avatar">
                                            <strong><?= htmlspecialchars($post['username']) ?></strong>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="../inne/script.js"></script>
</body>
</html>