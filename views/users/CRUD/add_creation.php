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

    // Démarrer la session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Vérifications de base
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
        exit();
    }

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
        exit();
    }

    // Vérifier CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Token CSRF invalide']);
        exit();
    }

    // Récupération des données - ✅ CORRIGÉ
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Validation simple - ✅ CORRIGÉ
    if (empty($title) || empty($description)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
        exit();
    }

    // Gestion de l'image
    $image_path = 'images/default.jpg'; // Image par défaut
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../../images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (!in_array(strtolower($extension), $allowed_extensions)) {
            echo json_encode(['success' => false, 'message' => 'Format image non autorisé']);
            exit();
        }
        
        $filename = uniqid() . '.' . $extension;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image_path = 'images/' . $filename;
        }
    }

    // Insertion en base - ✅ CORRIGÉ
    $stmt = $pdo->prepare("INSERT INTO creations (title, image, description, created_at) VALUES (?, ?, ?, NOW())");
    
    if ($stmt->execute([$title, $image_path, $description])) {
        echo json_encode([
            'success' => true, 
            'message' => 'Création ajoutée avec succès',
            'creation_id' => $pdo->lastInsertId()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'insertion']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}

// // Désactiver l'affichage des erreurs pour éviter la pollution HTML
// ini_set('display_errors', 0);
// error_reporting(0);

// // Headers JSON en premier
// header('Content-Type: application/json');

// try {
//     require_once '../../../config/DB.php';
//     require_once '../../../config/auth.php';
//     require_once '../../../config/functions.php';

//     // Démarrer la session
//     if (session_status() === PHP_SESSION_NONE) {
//         session_start();
//     }

//     // Vérifications de base
//     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//         echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
//         exit();
//     }

//     if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//         echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
//         exit();
//     }

//     // Récupération des données
//     $title = trim($_POST['title'] ?? '');
//     $description = trim($_POST['description'] ?? '');
//     // $price = floatval($_POST['price'] ?? 0);
//     // $stock = intval($_POST['stock'] ?? 0);
//     // $category_id = intval($_POST['category_id'] ?? 0);
    
//     // Validation simple
//     if (empty($title) || empty($description) ) {
//         echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
//         exit();
//     }

//     // Gestion basique de l'image (optionnelle pour le test)
//     $image_path = 'images/default.jpg'; // Image par défaut
    
//     if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
//         $upload_dir = '../../../images/';
//         if (!is_dir($upload_dir)) {
//             mkdir($upload_dir, 0755, true);
//         }
        
//         $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
//         $filename = uniqid() . '.' . $extension;
        
//         if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
//             $image_path = 'images/' . $filename;
//         }
//     }

//     // Tailles
//     // $sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : '';

//     // Insertion en base
//     $stmt = $pdo->prepare("INSERT INTO creations (title,image, created_at,description) VALUES (?, ?, NOW(), ? )");
    
//     if ($stmt->execute([$title,$image_path, $description])) {
//         echo json_encode([
//             'success' => true, 
//             'message' => 'Produit ajouté avec succès',
//             'product_id' => $pdo->lastInsertId()
//         ]);
//     } else {
//         echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'insertion']);
//     }

// } catch (Exception $e) {
//     echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
// }

// IMPORTANT: Pas de code après cette ligne
// Pas de
// fetch('CRUD/add_product.php', {
//     method: 'POST',
//     body: formData
// })
// .then(response => response.text()) // Changé en .text() pour voir le contenu brut
// .then(data => {
//     console.log('=== RÉPONSE BRUTE ===');
//     console.log(data);
//     console.log('=== FIN RÉPONSE ===');
    
//     // Essayer de parser le JSON
//     try {
//         const jsonData = JSON.parse(data);
//         console.log('JSON parsé avec succès:', jsonData);
//         // ... rest of your success code
//     } catch (e) {
//         console.error('Impossible de parser JSON:', e);
//         console.log('Caractères autour position 84:');
//         console.log('Avant:', data.substring(80, 84));
//         console.log('Position 84:', data.charAt(84));
//         console.log('Après:', data.substring(84, 90));
//     }
// })
// // Activer l'affichage des erreurs pour le debugging
// error_reporting(E_ALL);
// ini_set('display_errors', 0); // Désactivé pour éviter HTML dans la réponse JSON

// // Headers JSON en premier
// header('Content-Type: application/json');

// try {
//     require_once '../../../config/DB.php';
//     require_once '../../../config/auth.php';
//     require_once '../../../config/functions.php';

//     secure_session_start();

//     // Vérifier permissions admin
//     if (!is_logged_in() || !is_admin()) {
//         echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
//         exit();
//     }

//     // Vérifier la méthode
//     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//         echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
//         exit();
//     }

//     // Vérifier CSRF
//     if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
//         echo json_encode(['success' => false, 'message' => 'Token CSRF invalide']);
//         exit();
//     }

//     // Log pour debugging
//     error_log("Début ajout produit - Données reçues: " . print_r($_POST, true));

//     // Récupération des données
//     $name = trim($_POST['name'] ?? '');
//     $description = trim($_POST['description'] ?? '');
//     $price = floatval($_POST['price'] ?? 0);
//     $stock = intval($_POST['stock'] ?? 0);
//     $category_id = intval($_POST['category_id'] ?? 0);
//     $sizes = $_POST['sizes'] ?? [];

//     // Validation
//     if (empty($name)) {
//         echo json_encode(['success' => false, 'message' => 'Le nom du produit est requis']);
//         exit();
//     }
//     if (empty($description)) {
//         echo json_encode(['success' => false, 'message' => 'La description est requise']);
//         exit();
//     }
//     if ($price <= 0) {
//         echo json_encode(['success' => false, 'message' => 'Le prix doit être supérieur à 0']);
//         exit();
//     }
//     if ($stock < 0) {
//         echo json_encode(['success' => false, 'message' => 'Le stock ne peut pas être négatif']);
//         exit();
//     }
//     if ($category_id <= 0) {
//         echo json_encode(['success' => false, 'message' => 'Veuillez sélectionner une catégorie']);
//         exit();
//     }

//     // Gestion de l'image
//     $image_path = '';
//     if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
//         $upload_dir = '../../../images/products/';
        
//         if (!is_dir($upload_dir)) {
//             mkdir($upload_dir, 0755, true);
//         }
        
//         $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
//         $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        
//         if (!in_array($file_extension, $allowed_extensions)) {
//             echo json_encode(['success' => false, 'message' => 'Format image non autorisé']);
//             exit();
//         }
        
//         $new_filename = uniqid() . '.' . $file_extension;
//         $full_path = $upload_dir . $new_filename;
        
//         if (move_uploaded_file($_FILES['image']['tmp_name'], $full_path)) {
//             $image_path = 'images/products/' . $new_filename;
//         } else {
//             echo json_encode(['success' => false, 'message' => 'Erreur upload image']);
//             exit();
//         }
//     } else {
//         echo json_encode(['success' => false, 'message' => 'Image requise']);
//         exit();
//     }

//     // Traitement des tailles
//     $sizes_string = is_array($sizes) ? implode(',', $sizes) : '';

//     // Insertion en base
//     $stmt = $pdo->prepare("
//         INSERT INTO products (name, description, price, stock, category_id, image, size, created_at) 
//         VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
//     ");
    
//     $result = $stmt->execute([
//         $name, 
//         $description, 
//         $price, 
//         $stock, 
//         $category_id, 
//         $image_path, 
//         $sizes_string
//     ]);

//     if ($result) {
//         error_log("Produit ajouté avec succès - ID: " . $pdo->lastInsertId());
//         echo json_encode([
//             'success' => true, 
//             'message' => 'Produit ajouté avec succès',
//             'product_id' => $pdo->lastInsertId()
//         ]);
//     } else {
//         $errorInfo = $stmt->errorInfo();
//         error_log("Erreur SQL: " . print_r($errorInfo, true));
//         echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'insertion']);
//     }

// } catch (Exception $e) {
//     error_log("Exception dans add_product.php: " . $e->getMessage());
//     echo json_encode([
//         'success' => false, 
//         'message' => 'Erreur serveur: ' . $e->getMessage()
//     ]);
// }

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// require_once('../../../config/DB.php');
// require_once '../../../config/auth.php';
// require_once '../../../config/functions.php';
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $name = trim($_POST['name']);
//     $category_id= (int)$_POST['category_id'];
//     $price = trim($_POST['price']);
//     $stock = trim($_POST['stock']);
//     $description = trim($_POST['description']);
//     $image = trim($_POST['images']);
//     $size = trim($_POST['size']);


//     // Séparer nom et prénom
//     // $parts = explode(' ', $name, 2);
//     // $last_name = $parts[0];
//     // $first_name = isset($parts[1]) ? $parts[1] : '';
// $stmt = $pdo->prepare("INSERT INTO products 
//     (name, image, created_at, description, price, stock, size, category_id) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)");

// $stmt->execute([$name, $image, $description, $price, $stock, $size, $category_id]);

//     header('Content-Type: application/json');
// echo json_encode(['success' => true]);
// exit;
// }