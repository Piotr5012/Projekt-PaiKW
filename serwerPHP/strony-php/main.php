<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
    <link rel="stylesheet" href="../inne/main_style.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-left"></div>

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
    
    <div class="main-container">

        <?php if (isset($_SESSION['success_post'])): ?>
                <span class="text-sm text-success"><?= $_SESSION['success_post'] ?></span>
                <?php unset($_SESSION['success_post']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['logged_in']) && isset($_SESSION['user_username'])): ?>
            <h1>Witaj <?= htmlspecialchars($_SESSION['user_username']) ?></h1>
        <?php endif; ?>

        <div class="tiles-wrapper">
            <div class="menu-tile">
                <a href="../strony-php/posts_overview.php" class="tile-button">
                    Przeglądaj posty
                </a>
            </div>

            <div class="menu-tile">
                <a href="../strony-php/profile.php" class="tile-button">
                    Profil
                </a>
            </div>

            <div class="menu-tile">
                <a href="../strony-php/followers.php" class="tile-button">
                    Obserwowani
                </a>
            </div>
        
        </div>


    </div>


    <script src="../inne/script.js"></script>
    
</body>
</html>