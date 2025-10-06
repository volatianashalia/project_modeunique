<?php
require_once('../../../config/DB.php');
require_once('../../../config/auth.php');
require_once('../../../config/functions.php');

// Sécuriser la session
secure_session_start();

// Vérifier les autorisations
if (!is_logged_in() || !is_admin()) {
    header('HTTP/1.0 403 Forbidden');
    die('Accès non autorisé');
}

// Vérifier la méthode HTTP et le token CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.0 405 Method Not Allowed');
    die('Méthode non autorisée');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    header('HTTP/1.0 400 Bad Request');
    die('Token CSRF invalide');
}

$response = ['success' => false, 'message' => ''];

try {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    
    if (!$id || $id <= 0) {
        $response['message'] = 'ID utilisateur invalide';
    } else {
        // Vérifier que l'utilisateur existe et n'est pas admin
        $checkStmt = $pdo->prepare("SELECT id, role, first_name, last_name FROM users WHERE id = ?");
        $checkStmt->execute([$id]);
        $user = $checkStmt->fetch();
        
        if (!$user) {
            $response['message'] = 'Utilisateur introuvable';
        } elseif ($user['role'] === 'admin') {
            $response['message'] = 'Impossible de supprimer un administrateur';
        } elseif ($user['id'] === $_SESSION['user_id']) {
            $response['message'] = 'Impossible de vous supprimer vous-même';
        } else {
            // Commencer une transaction
            $pdo->beginTransaction();
            
            try {
                // Supprimer les données liées en cascade
                $pdo->prepare("DELETE FROM appointments WHERE user_id = ?")->execute([$id]);
                $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$id]);
                
                // Mettre à jour les commandes au lieu de les supprimer (pour l'historique)
                $pdo->prepare("UPDATE orders SET user_id = NULL WHERE user_id = ?")->execute([$id]);
                
                // Supprimer l'utilisateur
                $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $deleteStmt->execute([$id]);
                
                if ($deleteStmt->rowCount() > 0) {
                    $pdo->commit();
                    
                    // Log de sécurité
                    log_security_event('user_deleted', [
                        'deleted_user_id' => $id,
                        'deleted_user_name' => $user['first_name'] . ' ' . $user['last_name'],
                        'admin_user_id' => $_SESSION['user_id']
                    ]);
                    
                    $response['success'] = true;
                    $response['message'] = 'Utilisateur supprimé avec succès';
                } else {
                    $pdo->rollback();
                    $response['message'] = 'Erreur lors de la suppression';
                }
            } catch (Exception $e) {
                $pdo->rollback();
                throw $e;
            }
        }
    }
} catch (Exception $e) {
    error_log("Erreur suppression utilisateur: " . $e->getMessage());
    $response['message'] = 'Erreur technique lors de la suppression';
}

// Réponse JSON pour AJAX ou redirection pour formulaire classique
if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    if ($response['success']) {
        redirect_with_message('../admin.php#clients', $response['message'], 'success');
    } else {
        redirect_with_message('../admin.php#clients', $response['message'], 'error');
    }
}
exit;
?>

<?php
// require_once('../../../config/DB.php');
// $id = intval($_GET['id']);
// $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
// $stmt->execute([$id]);
// header('Location: admin.php#clients');
// exit;
?>