<?php

// Désactiver l'affichage des erreurs pour éviter la pollution HTML
ini_set('display_errors', 0);
error_reporting(0);

// Headers JSON en premier
header('Content-Type: application/json');

try {
    require_once '../../../config/DB.php';
    require_once '../../../config/auth.php';
    require_once '../../../config/functions.php';

    // ✅ Utiliser la même fonction de démarrage de session sécurisée que le reste de l'application
    secure_session_start();
    // Vérifications de base
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
        exit();
    }

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
        exit();
    }

    // Récupération des données
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    
    // Validation simple
    if (empty($name) || empty($description) || $price <= 0 || $category_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
        exit();
    }

    // Gestion basique de l'image (optionnelle pour le test)
    $image_path = 'images/default.jpg'; // Image par défaut
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../../images/products/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image_path = 'images/products/' . $filename;
        }
    }

    // Tailles
    $sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : '';

    // Insertion en base
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, category_id, image, size, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    
    if ($stmt->execute([$name, $description, $price, $stock, $category_id, $image_path, $sizes])) {
        $new_product_id = $pdo->lastInsertId();

        // Récupérer les informations complètes du nouveau produit pour les renvoyer au front-end
        $productStmt = $pdo->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
        ");
        $productStmt->execute([$new_product_id]);
        $new_product = $productStmt->fetch();

        echo json_encode([
            'success' => true, 
            'message' => 'Produit ajouté avec succès',
            'product' => $new_product // Renvoyer l'objet produit complet
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'insertion']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
