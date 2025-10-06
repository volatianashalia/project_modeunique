<?php

header('Content-Type: application/json');

try {
    require_once 'DB.php';
    require_once 'auth.php';
    
    secure_session_start();
    
    if (!is_logged_in() || !is_admin()) {
        echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
        exit();
    }
    
    $stmt = $pdo->query("
        SELECT id, title, description, image, created_at
        FROM creations 
        ORDER BY created_at DESC
    ");
    
    $creations = $stmt->fetchAll();
    
    echo json_encode($creations);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}

