<?php
session_start();

if (isset($_SERVER['HTTP_REFERER']) && !strpos($_SERVER['HTTP_REFERER'], 'post.php')) {
    $_SESSION['last_list_page'] = $_SERVER['HTTP_REFERER'];
}


if (!isset($_SESSION['last_list_page'])) {
    $_SESSION['last_list_page'] = 'posts_overview.php';
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

require_once '../skrypty-php/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: posts_overview.php');
    exit();
}

$db = new Database();
$post = $db->getPostById($id);
$comments = $db->getCommentsByPost($id);

if (!$post) {
    die("Post nie istnieje.");
}


$userId = $_SESSION['user_id'];
$userVote = $db->getUserVote($post['id'], $userId);

function renderComments($comments, $postId, $db, $userId, $parentId = null, $level = 0) {
    foreach ($comments as $comment) {
        if ($comment['parent_id'] == $parentId) {
            $isOwner = ($userId && $userId == $comment['user_id']);
            
            $userCommentVote = $userId ? $db->getCommentVote($comment['id'], $userId) : null;
            ?>
            <div class="comment-item" style="margin-left: <?= $level * 30 ?>px;">
                <div class="comment-header">
                    <div class="comment-author-box">
                        <a href="profile.php?id=<?= $comment['user_id'] ?>" class="author-profile-link">
                            <img src="<?= htmlspecialchars($comment['avatar'] ?? '../inne/domyslny_avatar.png') ?>" alt="" class="comment-mini-avatar">
                            <span class="comment-user"><?= htmlspecialchars($comment['username']) ?></span>
                        </a>
                        <span class="comment-date"><?= date('d.m.Y H:i', strtotime($comment['data'])) ?></span>
                    </div>

                    <?php if ($isOwner): ?>
                        <a href="../skrypty-php/post-interaction.php?delete_comment=<?= $comment['id'] ?>" 
                           class="delete-comment-btn" 
                           onclick="return confirm('Czy na pewno chcesz usunąć ten komentarz?')">
                            <span class="icon-delete"></span>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="comment-content">
                    <?= nl2br(htmlspecialchars($comment['content'])) ?>
                </div>

                <div class="comment-actions">
                    <div class="comment-voting">
                        <a href="../skrypty-php/post-interaction.php?id=<?= $comment['id'] ?>&type=like&item=comment" 
                           class="vote-btn comment-vote like <?= ($userCommentVote === 'like') ? 'active' : '' ?>">
                            <span class="icon-vote icon-like"></span> 
                            <span class="count"><?= $comment['likes_count'] ?? 0 ?></span>
                        </a>
                        
                        <a href="../skrypty-php/post-interaction.php?id=<?= $comment['id'] ?>&type=dislike&item=comment" 
                           class="vote-btn comment-vote dislike <?= ($userCommentVote === 'dislike') ? 'active' : '' ?>">
                            <span class="icon-vote icon-dislike"></span> 
                            <span class="count"><?= $comment['dislikes_count'] ?? 0 ?></span>
                        </a>
                    </div>
                    
                    <button class="reply-btn" 
                            data-comment-id="<?= $comment['id'] ?>" 
                            data-post-id="<?= $postId ?>">
                        Odpowiedz
                    </button>
                </div>

                <div id="reply-form-container-<?= $comment['id'] ?>" class="reply-form-wrapper"></div>
                
                <?php 
                renderComments($comments, $postId, $db, $userId, $comment['id'], $level + 1);
                ?>
            </div>
            <?php
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="../inne/main_style.css">
    <link rel="stylesheet" href="../inne/post_look.css">
    <link rel="stylesheet" href="../inne/comment_votes.css">

    <script src="../inne/profileshow.js" defer></script>

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

<div class="post-container">

    <div class="back-btn-container">
        <a href="javascript:void(0)" onclick="smartBack('<?= $_SESSION['last_list_page'] ?>')" class="back-btn">← Powrót</a>
    </div>

    <div class="post-card">

        <?php if (!empty($post['image'])): ?>
            <div class="post-image-container"> 
                <img src="<?= $post['image'] ?>" class="post-image-content">
            </div>
        <?php endif; ?>

        <div class="post-date-strip">
            Opublikowano: <?= date('d.m.Y H:i', strtotime($post['data'])) ?>
        </div>

        <div class="post-body">
            <h1 class="post-title-main">
                <?= htmlspecialchars($post['title']) ?>
            </h1>

            <p class="post-text-full">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </p>

            <div class="post-footer-flex">

                <div class="post-footer-left">
                    <span class="category-tag"><?= $post['category_name'] ?></span>
                </div>

                <div class="rating-section">
                    <a href="../skrypty-php/post-interaction.php?id=<?= $post['id'] ?>&type=like" 
                    class="vote-btn like <?= ($userVote === 'like') ? 'active' : '' ?>" title="Lubię to">
                        <span class="icon-vote icon-like"></span> 
                        <span class="count"><?= $post['likes_count'] ?? 0 ?></span>
                    </a>
                    
                    <a href="../skrypty-php/post-interaction.php?id=<?= $post['id'] ?>&type=dislike" 
                    class="vote-btn dislike <?= ($userVote === 'dislike') ? 'active' : '' ?>" title="Nie lubię">
                        <span class="icon-vote icon-dislike"></span> 
                        <span class="count"><?= $post['dislikes_count'] ?? 0 ?></span>
                    </a>
                </div>

                <div class="post-footer-right">
                    <span class="post-author-info">
                        Autor: 
                        <a href="profile.php?id=<?= $post['user_id'] ?>" class="author-profile-link">
                            <img src="<?= htmlspecialchars($post['user_avatar'] ?? '../inne/domyslny_avatar.png') ?>" alt="" class="author-mini-avatar">
                            <strong><?= htmlspecialchars($post['user_name']) ?></strong>
                        </a>
                    </span>
                </div>

            </div>
        </div>
    </div>

    <div class="comments-section">

        <h3 class="comments-title">Komentarze (<?= count($comments) ?>)</h3>

        <?php if (isset($_SESSION['logged_in'])): ?>
            <div class="comment-form-main">
                <form action="../skrypty-php/post-interaction.php" method="POST">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <textarea name="content" required placeholder="Napisz komentarz..."></textarea>

                    <div class="comment-submit-wrapper">
                        <button type="submit" class="comment-submit-btn">Dodaj komentarz</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <p class="login-info">
                Musisz się <a href="../index.php">zalogować</a>, aby komentować.
            </p>
        <?php endif; ?>

        <div class="comments-list">
            <?php if (empty($comments)): ?>
                <p>Brak komentarzy. Bądź pierwszy!</p>
            <?php else: ?>
                <?php 
                    $userId = $_SESSION['user_id'] ?? null;
                    renderComments($comments, $post['id'], $db, $userId);
                ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<script src="../inne/comments_engine.js"></script>
</body>
</html>