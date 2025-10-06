<?php
header('Content-Type: application/json');

try {
    require_once '../../../config/DB.php';
    require_once '../../../config/auth.php';
    require_once '../../../config/functions.php';

    secure_session_start();

    // 1. Vérifications de sécurité
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }

    if (!is_logged_in() || !is_admin()) {
        throw new Exception('Accès non autorisé');
    }

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        throw new Exception('Token CSRF invalide');
    }

    // 2. Récupération et validation des données
    $action = $_POST['action'] ?? '';
    $review_id = intval($_POST['review_id'] ?? 0);

    if (empty($action) || $review_id <= 0) {
        throw new Exception('Données invalides.');
    }

    // 3. Traitement de l'action
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE reviews SET is_visible = 1 WHERE id = ?");
        $stmt->execute([$review_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Avis approuvé avec succès.']);
        } else {
            throw new Exception('Impossible d\'approuver l\'avis.');
        }
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$review_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Avis supprimé avec succès.']);
        } else {
            throw new Exception('Impossible de supprimer l\'avis.');
        }
    } else {
        throw new Exception('Action non reconnue.');
    }

} catch (Exception $e) {
    error_log("Erreur dans manage_review.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
