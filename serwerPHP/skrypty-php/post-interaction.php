<?php
session_start();
require_once 'database.php';

$db = new Database();

if (!isset($_SESSION['logged_in'])) {
    if (isset($_GET['ajax'])) {
        echo json_encode(['success' => false, 'error' => 'not_logged_in']);
        exit;
    }
    header("Location: ../strony-php/login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// polubienia
if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];
    $itemType = $_GET['item'] ?? 'post';

    if ($type === 'like' || $type === 'dislike') {
        
        if ($itemType === 'comment') {
            $db->RateComment($id, $userId, $type);
        } else {
            $db->Rate($id, $userId, $type);
            if (isset($_GET['ajax'])) {
                $postStats = $db->getPostById($id);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'likes' => $postStats['likes_count'],
                    'dislikes' => $postStats['dislikes_count']
                ]);
                exit;
            }
        }
    }
}

// komentarze
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $postId = $_POST['post_id'];
    $content = trim($_POST['content']);
    $parentId = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

    if (!empty($content)) {
        $db->addComment($postId, $userId, $content, $parentId);
    }
}

// usuwanie komentarzy
if (isset($_GET['delete_comment'])) {
    $commentId = (int)$_GET['delete_comment'];
    $comment = $db->getCommentById($commentId);
    
    if ($comment && $comment['user_id'] == $userId) {
        $db->deleteComment($commentId);
    }
}
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../strony-php/posts_overview.php'));
exit;