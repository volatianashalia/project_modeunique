<?php
// views/users/CRUD/update_order_status.php
header('Content-Type: application/json');

require_once '../../../config/DB.php';
require_once '../../../config/auth.php';
require_once '../../../config/functions.php';

secure_session_start();

try {
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
    $order_id = intval($_POST['order_id'] ?? 0);
    $new_status = trim($_POST['new_status'] ?? '');
    $allowed_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    if ($order_id <= 0) {
        throw new Exception('ID de commande invalide.');
    }
    if (empty($new_status) || !in_array($new_status, $allowed_statuses)) {
        throw new Exception('Statut invalide.');
    }

    // 3. Récupérer les informations de la commande (email client, etc.)
    $stmt = $pdo->prepare("
        SELECT o.id, o.order_number, o.full_name, o.email, u.first_name, u.last_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id = ? 
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        throw new Exception('Commande non trouvée.');
    }

    // 4. Mettre à jour le statut dans la base de données
    $updateStmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $updateStmt->execute([$new_status, $order_id]);

    // 5. Envoyer un email si le statut est "expédiée" ou "livrée"
    if (in_array($new_status, ['shipped', 'delivered'])) {
        sendOrderStatusEmail($order, $new_status);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Statut de la commande mis à jour avec succès.'
    ]);

} catch (Exception $e) {
    error_log("Erreur dans update_order_status.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Envoie un email au client pour l'informer du changement de statut
 * Cette fonction utilise la fonction globale send_email()
 */
function sendOrderStatusEmail($order, $new_status) {
    $customer_email = $order['email'];
    $customer_name = $order['full_name'] ?: ($order['first_name'] . ' ' . $order['last_name']);
    $order_number = $order['order_number'];
    
    $email_data = [
        'shipped' => [
            'subject' => "Votre commande #{$order_number} a été expédiée !",
            'body' => "<p>Bonne nouvelle ! Votre commande <strong>#{$order_number}</strong> vient d'être expédiée.</p><p>Vous pouvez suivre son état depuis votre espace client.</p>"
        ],
        'delivered' => [
            'subject' => "Votre commande #{$order_number} a été livrée !",
            'body' => "<p>Votre commande <strong>#{$order_number}</strong> a bien été livrée.</p><p>Nous espérons que vos articles vous plaisent ! N'hésitez pas à nous laisser un avis.</p>"
        ]
    ];

    if (!isset($email_data[$new_status])) {
        return;
    }

    $subject = $email_data[$new_status]['subject'];
    $message_body = "<p>Bonjour " . htmlspecialchars($customer_name) . ",</p>" . $email_data[$new_status]['body'] . "<p>Cordialement,<br>L'équipe Mode Unique</p>";

    // Utilisation de la fonction send_email existante
    // Création d'un template HTML plus riche pour l'email
    $email_template = '
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
            .header { background-color: #D4AF37; color: #ffffff; padding: 25px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 30px; }
            .content p { margin-bottom: 15px; }
            .footer { background-color: #f4f4f4; color: #888; text-align: center; padding: 20px; font-size: 12px; }
            .button { display: inline-block; padding: 12px 25px; margin: 20px 0; background-color: #D4AF37; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Mode Unique</h1>
            </div>
            <div class="content">
                ' . $message_body . '
                <p style="text-align:center;">
                    <a href="http://' . $_SERVER['HTTP_HOST'] . '/ModeUnique/views/products/my_orders.php" class="button">Voir mes commandes</a>
                </p>
            </div>
            <div class="footer">
                <p>&copy; ' . date('Y') . ' Mode Unique. Tous droits réservés.</p>
                <p>Ceci est un e-mail automatique, merci de ne pas y répondre.</p>
            </div>
        </div>
    </body>
    </html>';

    try {
        // On passe le template HTML complet à la fonction send_email qui utilise PHPMailer
        send_email($customer_email, $subject, $email_template);
    } catch (Exception $e) {
        // Ne pas bloquer le processus si l'email échoue, mais logger l'erreur
        error_log("Échec de l'envoi de l'email de statut pour la commande {$order['id']}: " . $e->getMessage());
    }
}
?>
