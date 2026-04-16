<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../skrypty-php/database.php';
$db = new Database();

$search_query = $_GET['search'] ?? '';
$selected_cats = $_GET['categories'] ?? [];
$sort_by = $_GET['sort_by'] ?? 'data';

$posts = $db->getPosts($selected_cats, $sort_by, $search_query);
$all_categories = $db->getCategories();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Przegląd postów</title>
    <link rel="stylesheet" href="../inne/main_style.css">
    <link rel="stylesheet" href="../inne/posts_view.css">
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
                
                <h3 class="filter-title">Szukaj tytułu</h3>
                <div class="filter-list filter-list--search">
                    <input type="text" name="search" placeholder="Wpisz frazę..."
                        value="<?= htmlspecialchars($search_query) ?>"
                        class="filter-input">
                </div>

                <h3 class="filter-title">Kategorie</h3>
                <div class="filter-list">
                    <?php foreach ($all_categories as $cat): ?>
                        <label class="filter-item">
                            <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" 
                                <?= in_array($cat['id'], $selected_cats) ? 'checked' : '' ?>>
                            <span class="custom-checkbox"></span>
                            <?= htmlspecialchars($cat['name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <h3 class="filter-title" style="margin-top: 25px;">Sortuj według</h3>
                <div class="filter-list">
                    <label class="filter-item">
                        <input type="radio" name="sort_by" value="data" <?= ($sort_by === 'data') ? 'checked' : '' ?>>
                        <span class="custom-radio"></span> 
                        Najnowsze
                    </label>
                    
                    <label class="filter-item">
                        <input type="radio" name="sort_by" value="most_liked" <?= ($sort_by === 'most_liked') ? 'checked' : '' ?>>
                        <span class="custom-radio"></span> 
                        Najlepiej oceniane
                    </label>
                    
                    <label class="filter-item">
                        <input type="radio" name="sort_by" value="most_disliked" <?= ($sort_by === 'most_disliked') ? 'checked' : '' ?>>
                        <span class="custom-radio"></span> 
                        Najgorzej oceniane
                    </label>
                </div>

                <button type="submit" class="simple-submit-btn filter-submit">Szukaj</button>
                
                <?php if (!empty($selected_cats) || $sort_by !== 'data' || !empty($search_query)): ?>
                    <a href="?" class="clear-filters" style="display: block; text-align: center; margin-top: 10px; color: var(--MAIN); text-decoration: none; font-size: 0.9em;">Wyczyść wszystko</a>
                <?php endif; ?>
            </form>
        </aside>

        <div class="overview-container">
            <?php if (empty($posts)): ?>
                <div class="no-posts">
                    <p>Brak postów spełniających kryteria wyszukiwania.</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <?php if (!empty($post['image'])): ?>
                            <div class="post-image">
                                <img src="<?= $post['image'] ?>" alt="Zdjęcie posta">
                            </div>
                        <?php endif; ?>

                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="post.php?id=<?= $post['id'] ?>" class="post-link">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h2>
                            <p class="post-text"><?= nl2br(htmlspecialchars(mb_strimwidth($post['content'], 0, 200, "..."))) ?></p>
                            
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
                                        <a href="profile.php?id=<?= (int)$post['user_id'] ?>" class="author-profile-link">
                                            <?php 
                                                $avatarPath = !empty($post['user_avatar']) ? $post['user_avatar'] : '../avatars/defoult_avatar/user.png';
                                            ?>
                                            <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Avatar" class="author-mini-avatar">
                                            <strong><?= htmlspecialchars($post['user_name']) ?></strong>
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