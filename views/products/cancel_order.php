<?php
session_start();
require_once '../../config/DB.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit();
}

$order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'ID invalide']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT id, status FROM orders 
        WHERE id = ? AND user_id = ? AND status = 'pending'
    ");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch();

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Commande introuvable ou non annulable']);
        exit();
    }
    $updateStmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
    $updateStmt->execute([$order_id]);
    $itemsStmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
    $itemsStmt->execute([$order_id]);
    $items = $itemsStmt->fetchAll();

    $restockStmt = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
    foreach ($items as $item) {
        $restockStmt->execute([$item['quantity'], $item['product_id']]);
    }

    echo json_encode(['success' => true, 'message' => 'Commande annulée']);

} catch (Exception $e) {
    error_log("Erreur annulation: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur système']);
}