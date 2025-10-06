<?php
require_once '../../../config/DB.php';
require_once '../../../config/auth.php';
require_once '../../../config/functions.php';

secure_session_start();
header('Content-Type: application/json');

if (!is_logged_in() || !is_admin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

try {
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        throw new Exception('ID de message invalide.');
    }

    // Tenter de mettre à jour le statut si le message est nouveau
    $updateStmt = $pdo->prepare("UPDATE contact_messages SET status = 'lu' WHERE id = ? AND status = 'nouveau'");
    $updateStmt->execute([$id]);

    // Dans tous les cas, récupérer les informations du message pour les renvoyer
    $selectStmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $selectStmt->execute([$id]);
    $message = $selectStmt->fetch(PDO::FETCH_ASSOC);

    if ($message) {
        echo json_encode(['success' => true, 'message' => 'Statut mis à jour.', 'data' => $message]);
    } else {
        throw new Exception('Message non trouvé.');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
