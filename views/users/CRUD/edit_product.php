<?php
ini_set('display_errors', 0);
error_reporting(0);

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
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $sizes = $_POST['sizes'] ?? [];

    if ($id <= 0) {
        throw new Exception('ID de produit invalide.');
    }
    if (empty($name) || empty($description) || $price <= 0 || $category_id <= 0) {
        throw new Exception('Tous les champs sont requis et doivent être valides.');
    }


    $sql_parts = [];
    $params = [];

    // Champs texte et numériques
    $sql_parts[] = "name = ?";
    $params[] = $name;
    $sql_parts[] = "description = ?";
    $params[] = $description;
    $sql_parts[] = "price = ?";
    $params[] = $price;
    $sql_parts[] = "stock = ?";
    $params[] = $stock;
    $sql_parts[] = "category_id = ?";
    $params[] = $category_id;

    // Tailles (converties en JSON)
    $sizes_json = json_encode($sizes);
    $sql_parts[] = "size = ?";
    $params[] = $sizes_json;

    // 4. Gestion de l'image (si une nouvelle est fournie)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Supprimer l'ancienne image si elle existe
        $oldImageStmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $oldImageStmt->execute([$id]);
        $oldImagePath = $oldImageStmt->fetchColumn();
        if ($oldImagePath && file_exists('../../../' . $oldImagePath)) {
            unlink('../../../' . $oldImagePath);
        }

        // Uploader la nouvelle image
        $upload_dir = '../../../images/products/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0775, true);
        }
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image_path = 'images/products/' . $filename;
            $sql_parts[] = "image = ?";
            $params[] = $image_path;
        }
    }

    // 5. Exécution de la mise à jour
    if (count($sql_parts) > 0) {
        $sql = "UPDATE products SET " . implode(', ', $sql_parts) . " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {            
            echo json_encode([
                'success' => true,
                'message' => 'Produit modifié avec succès'
            ]);
        } else {
            throw new Exception('Erreur lors de la mise à jour dans la base de données.');
        }
    } else {
        // Aucune modification détectée
        echo json_encode(['success' => true, 'message' => 'Aucune modification détectée.']);
    }

} catch (Exception $e) {
    // Log de l'erreur pour le débogage
    error_log("Error in edit_product.php: " . $e->getMessage() . " for data: " . print_r($_POST, true));
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}