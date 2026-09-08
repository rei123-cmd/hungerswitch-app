<?php
session_start();

$_SESSION['visited'] = true;

if(isset($_POST['preference'])) {
    $_SESSION['user_preference'] = $_POST['preference'];
}
setcookie('visited', 'true', time() + (86400 * 30), '/');

if(isset($_POST['preference'])) {
    setcookie('user_preference', $_POST['preference'], time() + (86400 * 30), '/');
}

echo json_encode(['status' => 'success']);
?>