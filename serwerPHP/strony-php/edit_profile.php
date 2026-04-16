<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

require_once '../skrypty-php/database.php';
$db = new Database();

$userId = $_SESSION['user_id'];
$user = $db->getUserById($userId); 

if (!$user) {
    die("Błąd: Nie znaleziono użytkownika.");
}

$old_data = $_SESSION['form_data_edit'] ?? [];
unset($_SESSION['form_data_edit']);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ustawienia profilu - <?= htmlspecialchars($user['username']) ?></title>
    <link rel="stylesheet" href="../inne/main_style.css"> 
    <link rel="stylesheet" href="../inne/edit_profile.css"> 

    <script src="../inne/profileshow.js"></script>

</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <a href="profile.php?id=<?= $userId ?>" class="back-link-nav">← Powrót do profilu</a>
        </div>
        <div class="nav-center">
            <a href="main.php"><button type="button" class="epicbtn">MENU</button></a>
        </div>
        <div class="nav-content">
            <a href="../skrypty-php/logout.php" class="logout-link">Wyloguj się</a>
        </div>
    </nav>

    <div class="message-container">
        <?php if (isset($_SESSION['error_edit'])): ?>
            <span class="text-sm text-error-edit dynamic-error"><?= $_SESSION['error_edit'] ?></span>
            <?php unset($_SESSION['error_edit']); ?>
        <?php endif; ?>
    </div>

    <div class="edit-page-wrapper">

        <div class="edit-profile-card">
            
            <form action="../skrypty-php/update_profile.php" method="POST" enctype="multipart/form-data">

                <div class="avatar-edit-section">
                    <div class="avatar-frame">
                        <img src="<?= htmlspecialchars($user['avatar'] ?? '../inne/domyslny_avatar.png') ?>" alt="Avatar" id="avatar-preview">
                    </div>
                    <input type="file" name="avatar" id="avatar_upload" style="display: none;" accept="image/*">
                    <label for="avatar_upload" class="edit-avatar-label">Zmień avatar</label>
                </div>

                <div class="form-inputs-section">
    
                    <div class="input_box_edit">
                        <label>Adres E-mail:</label> <input type="email" name="email" value="<?= htmlspecialchars($old_data['email'] ?? $user['email']) ?>" required>
                    </div>

                    <div class="input_box_edit">
                        <label>Nazwa użytkownika (username):</label> <input type="text" name="username" value="<?= htmlspecialchars($old_data['username'] ?? $user['username']) ?>" required>
                    </div>

                    <div class="input_box_edit textarea_edit">
                        <label>Opis profilu (bio):</label> <textarea name="bio" rows="4" placeholder="Opis profilu (bio)..."><?= htmlspecialchars(($old_data['bio'] ?? $user['bio']) ?? '') ?></textarea>
                    </div>

                    <div class="input_box_edit">
                        <label>Obecne hasło (podaj tylko jeśli chcesz ustawić nowe):</label> 
                        <input type="password" name="current_password">
                    </div>

                    <div class="input_box_edit">
                        <label>NOWE hasło:</label> 
                        <input type="password" name="new_password">
                    </div>
                </div>

                <div class="form-submit-section">
                    <button type="submit" class="simple-submit-btn">Zapisz</button>
                </div>

            </form>
        </div>

        <div class="delete-account-container">
            <a href="../strony-php/delete_account_confirm.php" class="delete-account-link">Usuń konto</a>
        </div>

    </div>

    <script src="../inne/script.js" defer></script>
</body>
</html>