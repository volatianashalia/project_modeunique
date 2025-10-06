<?php
// ===== 2. CRUD/add_category.php (VERSION SIMPLIFIÉE) =====
require_once '../../../config/DB.php';
require_once '../../../config/auth.php';

// Désactiver l'affichage des erreurs pour une réponse JSON propre
ini_set('display_errors', 0);
error_reporting(0);
require_once '../../../config/functions.php';

secure_session_start();

// Vérifier les permissions admin
if (!is_logged_in() || !is_admin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Vérifier le token CSRF
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            throw new Exception('Token CSRF invalide');
        }
        
        $name = trim($_POST['name'] ?? '');
        $image_path = '';
        
        // Validation
        if (empty($name)) {
            throw new Exception('Le nom de la catégorie est obligatoire');
        }
        
        if (strlen($name) > 100) {
            throw new Exception('Le nom de la catégorie ne peut pas dépasser 100 caractères');
        }
        
        // Vérifier si la catégorie existe déjà
        $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $checkStmt->execute([$name]);
        if ($checkStmt->fetch()) {
            throw new Exception('Une catégorie avec ce nom existe déjà');
        }
        
        // Gestion de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../../images/categories/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (!in_array($extension, $allowed_extensions)) {
                throw new Exception('Format d\'image non autorisé.');
            }
            
            $filename = uniqid('cat_') . '.' . $extension;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                $image_path = 'images/categories/' . $filename;
            }
        }
        
        // Insérer la nouvelle catégorie
        $stmt = $pdo->prepare("INSERT INTO categories (Name, image) VALUES (?, ?)");
        $result = $stmt->execute([$name, $image_path]);
        
        if ($result) {
            // Log de l'action
            log_security_event('category_added', [
                'admin_id' => $_SESSION['user_id'],
                'category_name' => $name
            ]);
            
            $new_category_id = $pdo->lastInsertId();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Catégorie ajoutée avec succès',
                'category' => [
                    'id' => $new_category_id,
                    'Name' => $name,
                    'image' => $image_path
                ]
            ]);
        } else {
            throw new Exception('Erreur lors de l\'ajout de la catégorie');
        }
        
    } catch (Exception $e) {
        error_log("Erreur add_category: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>