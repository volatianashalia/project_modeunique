<?php
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['user_id'])) {

        if (file_exists('../../config/functions.php')) {
            require_once '../../config/functions.php';
            if (function_exists('log_security_event')) {
                log_security_event('user_logout', ['user_id' => $_SESSION['user_id']]);
            }
        }
    }
    
    $_SESSION = array();
    

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
 
    if (!session_destroy()) {
        throw new Exception("Impossible de détruire la session.");
    }

    header('Location: ../../index.php?logout=success');
    exit();

} catch (Exception $e) {

    error_log("Erreur de déconnexion: " . $e->getMessage());
    header('Location: admin.php?logout_error=1');
    exit();
}
?>
