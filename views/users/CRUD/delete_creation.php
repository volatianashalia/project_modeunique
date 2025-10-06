<?php
header('Content-Type: application/json');

try {
    require_once '../../../config/DB.php';
    require_once '../../../config/auth.php';
    require_once '../../../config/functions.php';

    secure_session_start();

    if (!is_logged_in() || !is_admin()) {
        echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // SÉCURITÉ : Vérifier le token CSRF
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            throw new Exception('Token CSRF invalide');
        }

        $id = intval($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            exit();
        }

        // ✅ CORRIGÉ : Récupérer l'image pour la supprimer
        $stmt = $pdo->prepare("SELECT image FROM creations WHERE id = ?");
        $stmt->execute([$id]);
        $creation = $stmt->fetch();

        // ✅ CORRIGÉ : Supprimer la création
        $deleteStmt = $pdo->prepare("DELETE FROM creations WHERE id = ?");
        
        if ($deleteStmt->execute([$id])) {
            // ✅ CORRIGÉ : Supprimer l'image physique
            if ($creation && $creation['image'] && file_exists('../../../' . $creation['image'])) {
                unlink('../../../' . $creation['image']);
            }
            
            echo json_encode(['success' => true, 'message' => 'Création supprimée avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
// require '../../../config/DB.php';
// header('Content-Type: application/json');
// $id = intval($_GET['id']);
// $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
// $stmt->execute([$id]);
// header('Location: admin.php#products');
// exit;
// try {
//     $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
//     $stmt->execute([$id]);
//     echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
// } catch (Exception $e) {
//     echo json_encode(['success' => false, 'message' => 'Error deleting product: ' . $e->getMessage()]);
// }
// exit();

// header('Content-Type: application/json');

// try {
//     require_once '../../../config/DB.php';
//     require_once '../../../config/auth.php';
//     require_once '../../../config/functions.php';

//     secure_session_start();

//     if (!is_logged_in() || !is_admin()) {
//         echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
//         exit();
//     }

//     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//         // SÉCURITÉ : Vérifier le token CSRF
//         if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
//             throw new Exception('Token CSRF invalide');
//         }

//         $id = intval($_POST['id'] ?? 0);

//         if ($id <= 0) {
//             echo json_encode(['success' => false, 'message' => 'ID invalide']);
//             exit();
//         }

//         // Récupérer l'image pour la supprimer
//         $stmt = $pdo->prepare("SELECT image FROM creations WHERE id = ?");
//         $stmt->execute([$id]);
//         $product = $stmt->fetch();

//         // Supprimer le produit
//         $deleteStmt = $pdo->prepare("DELETE FROM creations WHERE id = ?");
        
//         if ($deleteStmt->execute([$id])) {
//             // Supprimer l'image physique
//             if ($creation && $creation['image'] && file_exists('../../../' . $creation['image'])) {
//                 unlink('../../../' . $creation['image']);
//             }
            
//             echo json_encode(['success' => true, 'message' => 'Produit supprimé avec succès']);
//         } else {
//             echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
//         }
//     }
// } catch (Exception $e) {
//     echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
// }
 