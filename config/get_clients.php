<?php
require 'DB.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT 
            users.id,
            users.first_name,
            users.last_name,
            users.email,
            users.status,
            users.created_at,
            COUNT(orders.id) AS totalOrders,
            COALESCE(SUM(orders.total_amount), 0) AS totalSpent
        FROM users
        LEFT JOIN orders ON users.id = orders.user_id
        WHERE users.role = 'client'
        GROUP BY users.id
        ORDER BY users.created_at DESC
    ");

    $clients = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $clients[] = [
            'id'          => $row['id'],
            'name'        => $row['first_name'] . ' ' . $row['last_name'],
            'email'       => $row['email'],
            'status'      => $row['status'] ?? 'Active',
            'created_at'  => $row['created_at'],
            'totalOrders' => (int) $row['totalOrders'],
            'totalSpent'  => (float) $row['totalSpent']
        ];
    }

    echo json_encode($clients);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
