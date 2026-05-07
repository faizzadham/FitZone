<?php
session_start();
$_SESSION = [];
session_destroy();
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}
header("Location: /FitZone/login.php");
exit();
?>
