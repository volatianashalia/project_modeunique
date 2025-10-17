<?php
session_start();
require_once __DIR__ '../../config/DB.php';
require_once __DIR__ '../../config/auth.php';

header('Content-Type: application/json');
if (!is_logged_in()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non connecté.']);
    exit();
}
$data = json_decode(file_get_contents('php://input'), true);
$appointment_id = $data['id'] ?? null;

if (!filter_var($appointment_id, FILTER_VALIDATE_INT)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de rendez-vous invalide.']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT id FROM appointments 
        WHERE id = ? AND user_id = ? AND status = 'confirmed'
    ");
    $stmt->execute([$appointment_id, $_SESSION['user_id']]);
    $appointment = $stmt->fetch();

    if (!$appointment) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Rendez-vous introuvable ou déjà annulé/terminé.']);
        exit();
    }
    $updateStmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
    $updateStmt->execute([$appointment_id]);

    echo json_encode(['success' => true, 'message' => 'Le rendez-vous a été annulé avec succès.']);

} catch (Exception $e) {
    error_log("Erreur d'annulation de RDV: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue. Veuillez réessayer.']);
}
