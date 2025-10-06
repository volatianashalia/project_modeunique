<?php
// Démarrer la session de manière sécurisée si elle n'est pas déjà active.
// C'est la première chose à faire.
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Log de sécurité si les fonctions existent
    if (isset($_SESSION['user_id'])) {
        // Inclure les fonctions nécessaires pour le logging
        if (file_exists('../../config/functions.php')) {
            require_once '../../config/functions.php';
            if (function_exists('log_security_event')) {
                log_security_event('user_logout', ['user_id' => $_SESSION['user_id']]);
            }
        }
    }
    
    // Vider toutes les variables de session
    $_SESSION = array();
    
    // Supprimer le cookie de session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Détruire la session et vérifier si cela a réussi
    if (!session_destroy()) {
        throw new Exception("Impossible de détruire la session.");
    }

    // Redirection vers la page d'accueil avec un message de succès
    header('Location: ../../index.php?logout=success');
    exit();

} catch (Exception $e) {
    // En cas d'erreur, logguer l'erreur et rediriger vers le panel admin avec un message d'erreur.
    error_log("Erreur de déconnexion: " . $e->getMessage());
    header('Location: admin.php?logout_error=1');
    exit();
}
?>