<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

$userId = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuwanie konta</title>
    <link rel="stylesheet" href="../inne/main_style.css">
    <link rel="stylesheet" href="../inne/edit_profile.css"> <style>
        .danger-zone {
            border-color: #ff0000 !important;
            box-shadow: 0 0 25px #ff0000 !important;
        }
        .warning-text {
            color: #ff0000;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .delete-btn {
            background-color: #ff0000 !important;
            color: white !important;
        }
        .delete-btn:hover {
            box-shadow: 0 0 20px #ff0000 !important;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <a href="edit_profile.php" class="back-link-nav">← Wróć do edycji</a>
        </div>
    </nav>

    <div class="edit-page-wrapper">
        <div class="edit-profile-card danger-zone">
            <h2 class="warning-text">Uwaga! Usuwasz konto</h2>
            <p style="color: var(--LETTERS); text-align: center; margin-bottom: 30px;">
                Ta operacja jest **nieodwracalna**. Wszystkie Twoje posty, zdjęcia oraz dane profilowe zostaną trwale usunięte z serwera.
            </p>

            <?php if (isset($_SESSION['error_delete'])): ?>
                <div style="text-align: center; margin-bottom: 15px;">
                    <span class="text-errordynamic"><?= $_SESSION['error_delete'] ?></span>
                </div>
                <?php unset($_SESSION['error_delete']); ?>
            <?php endif; ?>

            <form action="../skrypty-php/process_delete_account.php" method="POST">
                <div class="input_box_edit">
                    <label>Wpisz swoje hasło, aby potwierdzić:</label>
                    <input type="password" name="password_confirm" required>
                </div>

                <div class="form-submit-section">
                    <button type="submit" class="simple-submit-btn delete-btn">Potwierdzam - Usuń moje konto</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>