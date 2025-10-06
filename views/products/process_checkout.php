<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/DB.php';
require_once '../../config/functions.php'; 
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?redirect=checkout');
    exit();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php?error=empty_cart');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$postal_code = trim($_POST['postal_code'] ?? '');
$payment_method = $_POST['payment_method'] ?? '';
$notes = trim($_POST['notes'] ?? '');

$errors = [];

if (empty($full_name)) {
    $errors[] = "Le nom complet est requis.";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Une adresse email valide est requise.";
}

if (empty($phone)) {
    $errors[] = "Le numéro de téléphone est requis.";
}

if (empty($address)) {
    $errors[] = "L'adresse de livraison est requise.";
}

if (empty($city)) {
    $errors[] = "La ville est requise.";
}

if (empty($postal_code)) {
    $errors[] = "Le code postal est requis.";
}

if (empty($payment_method) || !in_array($payment_method, ['carte', 'paypal', 'virement', 'especes'])) {
    $errors[] = "Méthode de paiement invalide.";
}

if (!empty($errors)) {
    $_SESSION['checkout_errors'] = $errors;
    $_SESSION['checkout_data'] = $_POST;
    header('Location: checkout.php');
    exit();
}

try {
    $pdo->beginTransaction();
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    $order_number = 'CMD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            user_id, order_number, total_amount, status, 
            payment_method, payment_status,
            full_name, email, phone, 
            address, city, postal_code, 
            notes, created_at
        ) VALUES (
            ?, ?, ?, 'pending', 
            ?, 'pending',
            ?, ?, ?, 
            ?, ?, ?, 
            ?, NOW()
        )
    ");

    $stmt->execute([
        $user_id,
        $order_number,
        $total,
        $payment_method,
        $full_name,
        $email,
        $phone,
        $address,
        $city,
        $postal_code,
        $notes
    ]);

    $order_id = $pdo->lastInsertId();
    $stmt = $pdo->prepare("
        INSERT INTO order_items (
            order_id, product_id, product_name, 
            quantity, price, subtotal
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");

    $updateStockStmt = $pdo->prepare("
        UPDATE products 
        SET stock = stock - ? 
        WHERE id = ? AND stock >= ?
    ");

    foreach ($_SESSION['cart'] as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $stmt->execute([
            $order_id,
            $item['id'],
            $item['name'],
            $item['quantity'],
            $item['price'],
            $subtotal
        ]);
        $updateStockStmt->execute([
            $item['quantity'],
            $item['id'],
            $item['quantity']
        ]);
        if ($updateStockStmt->rowCount() === 0) {
            throw new Exception("Stock insuffisant pour le produit: " . $item['name']);
        }
    }
    $pdo->commit();
    try {
        sendOrderConfirmationEmail($email, $order_number, $total, $_SESSION['cart'], $full_name);
    } catch (Exception $e) {
        error_log("Erreur lors de l'envoi de l'email de confirmation: " . $e->getMessage());
    }
    unset($_SESSION['cart']);
    $_SESSION['order_success'] = [
        'order_number' => $order_number,
        'total' => $total,
        'email' => $email
    ];
    
    header('Location: order_success.php?order=' . $order_number);
    exit();

} catch (Exception $e) {

    $pdo->rollBack();
    
    error_log("Erreur lors du traitement de la commande: " . $e->getMessage());
    
    $_SESSION['checkout_errors'] = ["Une erreur est survenue lors du traitement de votre commande. Veuillez réessayer."];
    header('Location: checkout.php');
    exit();
}
function sendOrderConfirmationEmail($email, $order_number, $total, $items, $customer_name) {
    $to = $email;
    $subject = "Confirmation de commande - " . $order_number;
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #D4AF37; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background-color: #f2f2f2; }
            .total { font-size: 18px; font-weight: bold; color: #D4AF37; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Mode Unique</h1>
                <p>Confirmation de votre commande</p>
            </div>
            <div class='content'>
                <p>Bonjour " . htmlspecialchars($customer_name) . ",</p>
                <p>Merci pour votre commande ! Nous avons bien reçu votre commande.</p>
                <p><strong>Numéro de commande :</strong> {$order_number}</p>
                <p><strong>Total :</strong> " . number_format($total, 2) . " Ariary</p>
                
                <h3>Détails de votre commande :</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix</th>
                        </tr>
                    </thead>
                    <tbody>";
    
    foreach ($items as $item) {
        $message .= "
                        <tr>
                            <td>{$item['name']}</td>
                            <td>{$item['quantity']}</td>
                            <td>" . number_format($item['price'] * $item['quantity'], 2) . " €</td>
                        </tr>";
    }
    
    $message .= "
                    </tbody>
                </table>
                
                <p class='total'>Total : " . number_format($total, 2) . " €</p>
                
                <p>Vous recevrez une notification dès que votre commande sera expédiée.</p>
                <p>Cordialement,<br>L'équipe Mode Unique</p>
            </div>
            <div class='footer'>
                <p>&copy; 2025 Mode Unique - Tous droits réservés</p>
            </div>
        </div>
    </body>
    </html>
    ";
    send_email($to, $subject, $message);
}
?>
