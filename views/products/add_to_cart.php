<?php
session_start();
require_once '../../config/DB.php';
if (!isset($_SESSION['user_id'])) {
    $product_id_for_redirect = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    header('Location: ../../views/login.php?error=login_required&redirect=views/products/detail.php?id=' . $product_id_for_redirect);
    exit();
}
if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    header('Location: productPage.php?error=missing_data');
    exit();
}

$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if (!$product_id || !$quantity || $quantity < 1) {
    header('Location: productPage.php?error=invalid_data');
    exit();
}

try {

    $stmt = $pdo->prepare("SELECT id, name, price, stock, image FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        header('Location: productPage.php?error=product_not_found');
        exit();
    }

    if ($product['stock'] < $quantity) {
        header('Location: detail.php?id=' . $product_id . '&error=insufficient_stock');
        exit();
    }
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $new_quantity = $item['quantity'] + $quantity;
            if ($new_quantity > $product['stock']) {
                header('Location: detail.php?id=' . $product_id . '&error=insufficient_stock');
                exit();
            }
            $item['quantity'] = $new_quantity;
            $found = true;
            break;
        }
    } 
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image']
        ];
    }

    header('Location: cart.php?success=added');
    exit();

} catch (Exception $e) {
    error_log("Erreur add_to_cart: " . $e->getMessage());
    header('Location: detail.php?id=' . $product_id . '&error=system_error');
    exit();
}
?>