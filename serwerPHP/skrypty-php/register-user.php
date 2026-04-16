<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($_POST['username']) ||
        empty($_POST['email']) ||
        empty($_POST['password']) ||
        empty($_POST['verify-password'])
    ) {
        $_SESSION['error_register'] = 'Uzupełnij brakujące dane';
        header('Location: ../index.php?form=register');
        exit();
    }

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $verifyPassword = $_POST['verify-password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_register'] = 'Błędny Email';
        header('Location: ../index.php?form=register');
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['error_register'] = 'Hasło musi mieć co najmniej 8 znaków';
        header('Location: ../index.php?form=register');
        exit();
    }

    if ($password !== $verifyPassword) {
        $_SESSION['error_register'] = 'Hasła nie są takie same';
        header('Location: ../index.php?form=register');
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    require_once 'database.php';
    $db = new Database();
    
    try {
        $result = $db->createUser($username, $email, $hashedPassword);
        
        if ($result) {
            $_SESSION['success'] = 'Konto zostało utworzone pomyślnie!';
            $_SESSION['logged_in'] = true;
            $_SESSION['user_username'] = $username;
            header('Location: ../index.php?form=register');
            exit();
        } else {
            $_SESSION['error_register'] = 'Błąd: Nie udało się utworzyć konta';
            header('Location: ../index.php?form=register');
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error_register'] = 'Ten e-mail jest już zajęty';
        header('Location: ../index.php?form=register');
        exit();
    }
}

header('Location: ../index.php?form=register');
exit();