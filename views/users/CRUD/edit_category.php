<?php
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
        $name = trim($_POST['name'] ?? '');
        
        if ($id <= 0 || empty($name)) {
            throw new Exception('Données invalides');
        }
        
        // Vérifier si la catégorie existe
        $checkStmt = $pdo->prepare("SELECT Name, image FROM categories WHERE id = ?");
        $checkStmt->execute([$id]);
        $oldCategory = $checkStmt->fetch();
        
        if (!$oldCategory) {
            throw new Exception('Catégorie non trouvée');
        }
        
        // Vérifier unicité du nom (sauf pour la catégorie courante)
        $uniqueStmt = $pdo->prepare("SELECT id FROM categories WHERE Name = ? AND id != ?");
        $uniqueStmt->execute([$name, $id]);
        if ($uniqueStmt->fetch()) {
            throw new Exception('Une catégorie avec ce nom existe déjà');
        }
        
        // Gestion de l'image
        $imagePath = $oldCategory['image']; // Conserver l'ancienne image par défaut
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Définir le dossier d'upload
            $uploadDir = '../../../assets/images/categories/';
            
            // Créer le dossier s'il n'existe pas
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Vérifier le type de fichier
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = $_FILES['image']['type'];
            
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WEBP.');
            }
            
            // Vérifier la taille du fichier (5MB max)
            if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                throw new Exception('Le fichier est trop volumineux (5MB maximum).');
            }
            
            // Générer un nom de fichier unique
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'category_' . $id . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $filename;
            
            // Déplacer le fichier uploadé
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                // Supprimer l'ancienne image si elle existe
                if (!empty($oldCategory['image'])) {
                    $oldImagePath = '../../../' . $oldCategory['image'];
                    if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                // Chemin relatif pour la base de données
                $imagePath = 'assets/images/categories/' . $filename;
            } else {
                throw new Exception('Erreur lors de l\'upload de l\'image');
            }
        }
        
        // Mettre à jour avec ou sans nouvelle image
        $stmt = $pdo->prepare("UPDATE categories SET Name = ?, image = ? WHERE id = ?");
        $result = $stmt->execute([$name, $imagePath, $id]);
        
        if ($result) {
            log_security_event('category_updated', [
                'admin_id' => $_SESSION['user_id'],
                'category_id' => $id,
                'old_name' => $oldCategory['Name'],
                'new_name' => $name,
                'image_updated' => isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK
            ]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Catégorie modifiée avec succès',
                'data' => [
                    'id' => $id,
                    'name' => $name,
                    'image' => $imagePath
                ]
            ]);
        } else {
            throw new Exception('Erreur lors de la modification');
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>
<?php
// // ===== 3. CRUD/edit_category.php (VERSION SIMPLIFIÉE) =====
// require_once '../../../config/DB.php';
// require_once '../../../config/auth.php';
// require_once '../../../config/functions.php';

// secure_session_start();

// if (!is_logged_in() || !is_admin()) {
//     http_response_code(403);
//     echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
//     exit();
// }

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     try {
//         // Vérification CSRF
//         if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
//             throw new Exception('Token CSRF invalide');
//         }
        
//         $id = intval($_POST['id'] ?? 0);
//         $name = trim($_POST['name'] ?? '');
        
//         if ($id <= 0 || empty($name)) {
//             throw new Exception('Données invalides');
//         }
        
//         // Vérifier si la catégorie existe
//         $checkStmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
//         $checkStmt->execute([$id]);
//         $oldCategory = $checkStmt->fetch();
        
//         if (!$oldCategory) {
//             throw new Exception('Catégorie non trouvée');
//         }
        
//         // Vérifier unicité du nom (sauf pour la catégorie courante)
//         $uniqueStmt = $pdo->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
//         $uniqueStmt->execute([$name, $id]);
//         if ($uniqueStmt->fetch()) {
//             throw new Exception('Une catégorie avec ce nom existe déjà');
//         }
        
//         // Mettre à jour
//         $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
//         $result = $stmt->execute([$name, $id]);
        
//         if ($result) {
//             log_security_event('category_updated', [
//                 'admin_id' => $_SESSION['user_id'],
//                 'category_id' => $id,
//                 'old_name' => $oldCategory['name'],
//                 'new_name' => $name
//             ]);
            
//             echo json_encode(['success' => true, 'message' => 'Catégorie modifiée avec succès']);
//         } else {
//             throw new Exception('Erreur lors de la modification');
//         }
        
//     } catch (Exception $e) {
//         echo json_encode(['success' => false, 'message' => $e->getMessage()]);
//     }
// }
?>