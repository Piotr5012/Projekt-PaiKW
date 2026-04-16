<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $db = new Database();
    $userId = $_SESSION['user_id'];
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    $user = $db->getUserById($userId);

    if (!$user || !password_verify($passwordConfirm, $user['password'])) {
        $_SESSION['error_delete'] = "Nieprawidłowe hasło. Konto nie zostało usunięte.";
        header('Location: ../strony-php/delete_account_confirm.php');
        exit();
    }

    $userPosts = $db->getPostsByUserId($userId); 
    foreach ($userPosts as $post) {
        if (!empty($post['image']) && file_exists($post['image'])) {
            unlink($post['image']);
        }
    }

    if (!empty($user['avatar']) && strpos($user['avatar'], 'users_modif_avatars') !== false) {
        if (file_exists($user['avatar'])) {
            unlink($user['avatar']);
        }
    }

    if ($db->deleteUser($userId)) {
        session_destroy();
        session_start();
        $_SESSION['success_login'] = "Twoje konto zostało trwale usunięte.";
        header('Location: ../index.php');
        exit();
    } else {
        $_SESSION['error_delete'] = "Błąd techniczny podczas usuwania konta.";
        header('Location: ../strony-php/delete_account_confirm.php');
        exit();
    }

} else {
    header('Location: ../index.php');
    exit();
}