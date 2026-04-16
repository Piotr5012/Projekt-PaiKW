<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $_SESSION['form_data'] = $_POST;

    if (
        empty($_POST['title']) ||
        empty($_POST['content']) ||
        empty($_POST['category_id']) ||
        empty($_FILES['image']['name'])
    ) {
        $_SESSION['error_post'] = 'Uzupełnij brakujące dane';
        header('Location: ../strony-php/create_post.php');
        exit();
    }

    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $author_id = $_SESSION['user_id'];

    $file = $_FILES['image'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileType = $file['type'];

    $maxSize = 6 * 1024 * 1024;
    $allowedTypes = ["image/jpeg", "image/png", "image/jpg"];

    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['error_post'] = 'Błędne rozszerzenie pliku (dozwolone: JPG, PNG)';
        header('Location: ../strony-php/create_post.php');
        exit();
    }

    if ($fileSize > $maxSize) {
        $_SESSION['error_post'] = 'Za duży rozmiar pliku (max 6MB)';
        header('Location: ../strony-php/create_post.php');
        exit();
    }

    $fileNewName = uniqid() . "_" . $fileName;
    $image_directory = "../images/";
    $image_path = $image_directory . $fileNewName;

    if (move_uploaded_file($fileTmpName, $image_path)) {
        require_once 'database.php';
        $db = new Database();
        
        $result = $db->addPost($title, $content, $category_id, $author_id, $image_path);
            
        if ($result) {
            unset($_SESSION['form_data']);
            $_SESSION['success_post'] = 'Post został dodany';
            header('Location: ../strony-php/main.php');
            exit();
        } else {
            $_SESSION['error_post'] = 'Błąd bazy danych podczas dodawania posta';
            header('Location: ../strony-php/create_post.php');
            exit();
        }
    } else {
        $_SESSION['error_post'] = 'Nie udało się zapisać zdjęcia na serwerze';
        header('Location: ../strony-php/create_post.php');
        exit();
    }
}