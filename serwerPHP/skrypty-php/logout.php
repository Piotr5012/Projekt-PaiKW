<?php

session_start();

if (isset($_SESSION['logged_in']) && isset($_SESSION['user_username'])) {
    unset($_SESSION['logged_in']);
    unset($_SESSION['user_username']);
}

header('Location: ../index.php');
exit();