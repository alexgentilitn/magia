
<?php
require_once 'config.php';
requireLogin();

logActivity('logout', 'user', $_SESSION['user_id']);

session_destroy();
header('Location: login.php');
exit;
?>
