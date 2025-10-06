<?php
session_start();
header('Content-Type: application/json');

try {
    if (!isset($_POST['action'])) {
        throw new Exception('Action non spécifiée');
    }

    $action = $_POST['action'];
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    switch ($action) {
        case 'update':
            $index = filter_input(INPUT_POST, 'index', FILTER_VALIDATE_INT);
            $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
            
            if ($index === false || $quantity === false || $quantity < 1) {
                throw new Exception('Données invalides');
            }
            
            if (!isset($_SESSION['cart'][$index])) {
                throw new Exception('Produit non trouvé dans le panier');
            }
            
            $_SESSION['cart'][$index]['quantity'] = $quantity;
            
            echo json_encode([
                'success' => true,
                'message' => 'Quantité mise à jour'
            ]);
            break;
            
        case 'remove':
            $index = filter_input(INPUT_POST, 'index', FILTER_VALIDATE_INT);
            
            if ($index === false || !isset($_SESSION['cart'][$index])) {
                throw new Exception('Produit non trouvé dans le panier');
            }
            
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Produit supprimé'
            ]);
            break;
            
        default:
            throw new Exception('Action non reconnue');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>