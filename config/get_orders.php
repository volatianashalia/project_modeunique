<?php
require_once 'DB.php';
require_once 'auth.php';

secure_session_start();

if (!is_logged_in() || !is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Accès non autorisé']);
    exit();
}

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT 
            o.id, 
            o.order_number,
            o.total_amount, 
            o.status,
            o.payment_method,
            o.created_at,
            u.first_name, 
            u.last_name 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC 
        LIMIT 50
    ");
    
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

    error_log("Nombre de commandes trouvées: " . count($orders));
    
    echo json_encode($orders);
    
} catch (Exception $e) {
    error_log("Erreur get_orders.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}



