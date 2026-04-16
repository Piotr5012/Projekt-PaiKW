<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../skrypty-php/database.php';
$db = new Database();

$postId = $_GET['id'] ?? null;
if (!$postId) {
    header('Location: main.php');
    exit();
}

$post = $db->getPostById($postId);

if (!$post || $post['author_id'] != $_SESSION['user_id']) {
    header('Location: main.php');
    exit();
}

$categories = $db->getCategories();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj Post</title>
    <link rel="stylesheet" href="../inne/main_style.css">
    <link rel="stylesheet" href="../inne/add_post_style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="javascript:history.back()" class="back-link-nav" style="margin-left: 20px; text-decoration: none; color: var(--MAIN);">← Powrót</a>
        </div>
        
        <div class="nav-center">
            <a href="../strony-php/main.php">
                <button type="button" class="epicbtn">MENU</button>
            </a>
        </div>
        <div class="nav-content">
            <?php if (isset($_SESSION['logged_in'])): ?>
                <a href="../skrypty-php/logout.php" class="logout-link">Wyloguj się</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="addpost-contair">
        <h2 style="color: var(--MAIN); text-align: center; margin-bottom: 30px; text-transform: uppercase; letter-spacing: 2px;">Edycja posta</h2>

        <?php if (isset($_SESSION['error_post'])): ?>
            <span class="text-sm text-error dynamic-error"><?= $_SESSION['error_post'] ?></span>
            <?php unset($_SESSION['error_post']); ?>
        <?php endif; ?>

        <form action="../skrypty-php/update_post.php" method="post" enctype="multipart/form-data">
            
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">

            <div class="input_box">
                <input type="text" name="title" required value="<?= htmlspecialchars($post['title']) ?>">
                <label class="labelfortitle"> Tytuł posta:</label>
            </div>

            <div class="input_box textarea_wrapper">
                <textarea name="content" placeholder="Treść posta..."><?= htmlspecialchars($post['content']) ?></textarea>
            </div>

            <div class="row-container">
                <div class="input_box">
                    <select name="category_id" class="epic-select">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= ($post['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input_box file_wrapper">
                    <input class="uploadFIle" id="file_upload" type="file" name="image"/>
                    <label class="forLabel" for="file_upload"> Zmień zdjęcie </label>
                    <span class="chosefile_name" id="file-name">
                        <?= !empty($post['image']) ? 'Obecne: ' . basename($post['image']) : 'Brak zdjęcia' ?>
                    </span>
                </div>
            </div>

            <div class="input_box">
                <button type="submit" class="epicbtn">Zapisz zmiany</button>
            </div>
        </form>
    </div>

    <script src="../inne/script.js"></script>
</body>
</html>