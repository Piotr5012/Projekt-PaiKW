<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $db = new Database();
    
    $postId = (int)$_POST['post_id'];
    $userId = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $categoryId = (int)$_POST['category_id'];

    $oldPost = $db->getPostById($postId);

    if (!$oldPost || $oldPost['author_id'] != $userId) {
        header('Location: ../strony-php/main.php');
        exit();
    }

    $imagePath = $oldPost['image'];

    // 2. OBSŁUGA ZDJĘCIA
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        $fileTmp = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];
        $fileName = $_FILES['image']['name'];

        $fileType = mime_content_type($fileTmp);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $imageInfo = getimagesize($fileTmp);
        if ($imageInfo === false) {
            $_SESSION['error_post'] = 'Przesłany plik nie jest poprawnym obrazem.';
            header('Location: ../strony-php/edit_post.php?id=' . $postId);
            exit();
        }
        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['error_post'] = 'Nieprawidłowy format zdjęcia. Użyj JPG lub PNG.';
            header('Location: ../strony-php/edit_post.php?id=' . $postId);
            exit();
        }
        if (!in_array($fileExtension, $allowedExtensions)) {
            $_SESSION['error_post'] = 'Nieprawidłowe rozszerzenie pliku.';
            header('Location: ../strony-php/edit_post.php?id=' . $postId);
            exit();
        }
        if ($fileSize > $maxSize) {
            $_SESSION['error_post'] = 'Zdjęcie jest za duże. Maksymalny rozmiar to 2MB.';
            header('Location: ../strony-php/edit_post.php?id=' . $postId);
            exit();
        }

        $uploadDir = '../images/';
        $newFileName = uniqid('post_', true) . '.' . $fileExtension;
        $targetFile = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmp, $targetFile)) {
            if (!empty($oldPost['image']) && file_exists($oldPost['image'])) {
                unlink($oldPost['image']);
            }
            $imagePath = $targetFile;
        } else {
            $_SESSION['error_post'] = 'Błąd podczas zapisywania pliku na serwerze.';
            header('Location: ../strony-php/edit_post.php?id=' . $postId);
            exit();
        }

    } 
    elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        
        $errorCode = $_FILES['image']['error'];
        
        switch ($errorCode) {
            case 1:
            case 2:
                $_SESSION['error_post'] = 'Plik jest za duży (przekroczono limit serwera).';
                break;
            case 3: 
                $_SESSION['error_post'] = 'Zdjęcie zostało przesłane tylko częściowo. Spróbuj ponownie.';
                break;
            default:
                $_SESSION['error_post'] = 'Wystąpił problem podczas przesyłania zdjęcia (Kod: ' . $errorCode . ').';
                break;
        }

        header('Location: ../strony-php/edit_post.php?id=' . $postId);
        exit();
    }
    if ($db->updatePost($postId, $title, $content, $categoryId, $imagePath)) {
        $_SESSION['success_post'] = 'Post został pomyślnie zaktualizowany!';
        header('Location: ../strony-php/profile.php?id=' . $userId);
        exit();
    } else {
        $_SESSION['error_post'] = 'Błąd bazy danych podczas zapisu posta.';
        header('Location: ../strony-php/edit_post.php?id=' . $postId);
        exit();
    }

} else { 
    header('Location: ../index.php');
    exit();
}