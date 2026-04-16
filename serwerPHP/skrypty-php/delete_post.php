<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: ../strony-php/main.php');
    exit();
}

$db = new Database();
$postId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];

$post = $db->getPostById($postId);

if ($post && $post['author_id'] == $userId) {
    

    if (!empty($post['image'])) {
        $filePath = $post['image']; 
        
        if (file_exists($filePath)) {
            unlink($filePath); 
        }
    }

    if ($db->deletePost($postId)) {
        $_SESSION['success_post'] = "Post został trwale usunięty.";
    } else {
        $_SESSION['error_post'] = "Wystąpił błąd podczas usuwania posta.";
    }
}


header('Location: ../strony-php/profile.php?id=' . $userId);
exit();