<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($_POST['email']) ||
        empty($_POST['password'])
    )   {
        $_SESSION['error_login'] = 'Uzupełnij brakujące dane';
        header('Location: ../index.php');
        exit();
    }

    
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_login'] = 'Błędny Email';
        header('Location: ../index.php');
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['error_login'] = 'Hasło musi mieć co najmniej 8 znaków';
        header('Location: ../index.php');
        exit();
    }


    require_once 'database.php';
    $db = new Database();
    $result = $db->fetchByEmail($email);
    if ($result === false) {
        $_SESSION['error_login'] = 'Użytkownik nie istnieje';
        header('Location: ../index.php'); 
        exit();
    }
    if (!password_verify($password, $result['password'])) {
        $_SESSION['error_login'] = 'Błedne hasło';
        header('Location: ../index.php');
        exit();
    }
    $_SESSION['logged_in'] = true;
    $_SESSION['user_username'] = $result['username'];
    $_SESSION['user_id'] = $result['id'];

    header('Location: ../strony-php/main.php');
    exit();
} else {
    header('Location: ../index.php'); 
    exit();
}