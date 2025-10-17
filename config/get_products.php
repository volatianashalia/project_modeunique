<?php
require_once __DIR__ 'DB.php';
header('Content-Type: application/json');


try {
    $stmt = $pdo->query("
        SELECT 
            products.id,
            products.name,
            products.image,
            products.description,
            products.price,
            products.stock,
            products.size,
            products.category_id,
            products.created_at,
            categories.name AS category
        FROM products
        LEFT JOIN categories ON products.category_id = categories.id
        ORDER BY products.created_at DESC
    ");

    $products = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $products[] = [
            'id'          => $row['id'],
            'name'        => $row['name'],
            'category'    => $row['category'] ?? 'Uncategorized',
            'category_id' => $row['category_id'],
            'price'       => (float) $row['price'],
            'stock'       => $row['stock'],
            'description' => $row['description'],
            'image'       => '/ModeUnique/' . $row['image'],
            'created_at'  => $row['created_at'],
            'availability'=> $row['stock'] > 0 ? 'Available' : 'Out of Stock',
           'sizes_array' => json_decode($row['size'] ?? '[]', true) ?: []
        ];
    }

    echo json_encode($products);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
