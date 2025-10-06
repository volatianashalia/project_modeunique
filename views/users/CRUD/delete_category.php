<?php
// ===== 4. CRUD/delete_category.php =====
require_once '../../../config/DB.php';
require_once '../../../config/auth.php';
require_once '../../../config/functions.php';

secure_session_start();

if (!is_logged_in() || !is_admin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Vérification CSRF
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            throw new Exception('Token CSRF invalide');
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            throw new Exception('ID catégorie invalide');
        }
        
        // Vérifier s'il y a des produits dans cette catégorie
        $productCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $productCheckStmt->execute([$id]);
        $productCount = $productCheckStmt->fetchColumn();
        
        if ($productCount > 0) {
            throw new Exception('Impossible de supprimer une catégorie contenant des produits');
        }
        
        // Récupérer le nom avant suppression pour le log
        $nameStmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
        $nameStmt->execute([$id]);
        $categoryName = $nameStmt->fetchColumn();
        
        if (!$categoryName) {
            throw new Exception('Catégorie non trouvée');
        }
        
        // Supprimer la catégorie
        $deleteStmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $result = $deleteStmt->execute([$id]);
        
        if ($result) {
            log_security_event('category_deleted', [
                'admin_id' => $_SESSION['user_id'],
                'category_name' => $categoryName
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Catégorie supprimée avec succès']);
        } else {
            throw new Exception('Erreur lors de la suppression');
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>