<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/functions.php';
if (isset($_SESSION['user_id']) && file_exists('../config/functions.php')) {
    require_once '../config/functions.php';
    log_security_event('user_logout', ['user_id' => $_SESSION['user_id']]);
}

$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
header('Location: ../index.php');
exit();
?>