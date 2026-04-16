<?php
session_start();
require_once 'database.php';

if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $db = new Database();
    $followerId = $_SESSION['user_id'];
    $followedId = (int)$_GET['id'];
    if ($followerId !== $followedId) {
        $db->toggleFollow($followerId, $followedId);
    }
} 
if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: ../strony-php/main.php');
}
exit();