<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../skrypty-php/database.php';
$db = new Database();
$categories = $db->getCategories();

$old = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona od dodawania postów</title>
    <link rel="stylesheet" href="../inne/main_style.css">
    <link rel="stylesheet" href="../inne/add_post_style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left"></div>
        
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

        <?php if (isset($_SESSION['error_post'])): ?>
            <span class="text-sm text-error dynamic-error"><?= $_SESSION['error_post'] ?></span>
            <?php unset($_SESSION['error_post']); ?>
        <?php endif; ?>

        <form action="../skrypty-php/add-post.php" method="post" enctype="multipart/form-data">

            <div class="input_box">
                <input type="text" name="title" required value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                <label class="labelfortitle"> Tytuł posta:</label>
            </div>

            <div class="input_box textarea_wrapper">
                <textarea name="content" placeholder="Treść posta..."><?= htmlspecialchars($old['content'] ?? '') ?></textarea>
            </div>

            <div class="row-container">
                <div class="input_box">
                    <select name="category_id" class="epic-select">
                        <option value="" disabled <?= !isset($old['category_id']) ? 'selected' : '' ?>>Wybierz kategorię</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= (isset($old['category_id']) && $old['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input_box file_wrapper">
                    <input class="uploadFIle" id="file_upload" type="file" name="image"/>
                    <label class="forLabel" for="file_upload"> Dodaj zdjęcie </label>
                    <span class="chosefile_name" id="file-name"></span>
                </div>
            </div>

            <div class="input_box">
                <button type="submit" class="epicbtn">Opublikuj post</button>
            </div>
        </form>
    </div>

    <script src="../inne/script.js"></script>
</body>
</html>