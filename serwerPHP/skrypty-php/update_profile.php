<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $db = new Database();
    $userId = $_SESSION['user_id'];

    $user = $db->getUserById($userId);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $bio = trim($_POST['bio']);
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    $passwordToSave = $user['password']; 

    // -zmiana hasła
    if (!empty($newPassword)) {
        if (empty($currentPassword) || !password_verify($currentPassword, $user['password'])) {
            $_SESSION['error_edit'] = "Aby zmienić hasło, musisz podać poprawne obecne hasło.";
            header('Location: ../strony-php/edit_profile.php');
            exit();
        }

        if (strlen($newPassword) < 8) {
            $_SESSION['error_edit'] = "Nowe hasło musi mieć min. 8 znaków.";
            header('Location: ../strony-php/edit_profile.php');
            exit();
        }
        
        $passwordToSave = password_hash($newPassword, PASSWORD_DEFAULT);
    }

// avatar
$avatarPath = $user['avatar'];

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error_edit'] = "Błąd podczas przesyłania pliku.";

        switch ($_FILES['avatar']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $_SESSION['error_edit'] = "Plik jest za duży (limit serwera).";
                break;
        }

        header('Location: ../strony-php/edit_profile.php');
        exit();
    }

    $fileTmpPath = $_FILES['avatar']['tmp_name'];
    $fileSize = $_FILES['avatar']['size'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileType = finfo_file($finfo, $fileTmpPath);
    finfo_close($finfo);

    $allowedTypes = ["image/jpeg", "image/png"];

    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['error_edit'] = "Niedozwolony format zdjęcia. Użyj JPG lub PNG.";
        header('Location: ../strony-php/edit_profile.php');
        exit();
    }

    if ($fileSize > 2 * 1024 * 1024) {
        $_SESSION['error_edit'] = "Zdjęcie jest za duże. Maksymalny rozmiar to 2MB.";
        header('Location: ../strony-php/edit_profile.php');
        exit();
    }

    $uploadDir = '../avatars/users_modif_avatars/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileExtension = ($fileType === "image/png") ? "png" : "jpg";
    $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
    $targetFile = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $targetFile)) {

        if (!empty($user['avatar']) && strpos($user['avatar'], 'users_modif_avatars') !== false) {
            if (file_exists($user['avatar'])) {
                unlink($user['avatar']);
            }
        }

        $avatarPath = $targetFile;

    } else {
        $_SESSION['error_edit'] = "Nie udało się zapisać zdjęcia.";
        header('Location: ../strony-php/edit_profile.php');
        exit();
    }
}

    // zapis
    $result = $db->updateUserFull($userId, $username, $email, $bio, $avatarPath, $passwordToSave);

    if ($result) {
        $_SESSION['success_edit'] = "Zmiany zostały zapisane.";
        $_SESSION['user_username'] = $username;
        header('Location: ../strony-php/profile.php?id=' . $userId);
    } else {
        $_SESSION['error_edit'] = "Wystąpił błąd bazy danych.";
        header('Location: ../strony-php/edit_profile.php');
    }
    exit();
}