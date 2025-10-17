<?php
require_once __DIR__ '../../config/DB.php';
require_once __DIR__ '../../config/functions.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $firstName = trim($_POST['firstName'] ?? '');
        $lastName = trim($_POST['lastName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $errors = [];
        
        if (empty($firstName) || strlen($firstName) < 2) {
            $errors[] = "Le prénom doit contenir au moins 2 caractères.";
        }
        
        if (empty($lastName) || strlen($lastName) < 2) {
            $errors[] = "Le nom doit contenir au moins 2 caractères.";
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide.";
        }
        
        if (empty($subject)) {
            $errors[] = "Veuillez sélectionner un sujet.";
        }
        
        if (empty($message) || strlen($message) < 10) {
            $errors[] = "Le message doit contenir au moins 10 caractères.";
        }
        
        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errors)
            ]);
            exit;
        }

        $stmt = $pdo->prepare(
            "INSERT INTO contact_messages (first_name, last_name, email, phone, subject, message, status, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, 'nouveau', NOW())"
        );
        
        $result = $stmt->execute([
            $firstName,
            $lastName,
            $email,
            $phone,
            $subject,
            $message
        ]);
        
        if ($result) {
            $admin_email = "shaliapage2025@gmail.com"; 
            $email_subject = "Nouveau message de contact: " . htmlspecialchars($subject);
            $email_body = "
                <h1>Nouveau message depuis le site Mode Unique</h1>
                <p><strong>Nom :</strong> " . htmlspecialchars($firstName . ' ' . $lastName) . "</p>
                <p><strong>Email :</strong> " . htmlspecialchars($email) . "</p>
                <p><strong>Téléphone :</strong> " . htmlspecialchars($phone ?: 'Non fourni') . "</p>
                <hr>
                <h3>Message :</h3>
                <p style='background-color:#f4f4f4; padding:15px; border-radius:5px;'>" . nl2br(htmlspecialchars($message)) . "</p>
            ";

            $email_sent = send_email($admin_email, $email_subject, $email_body);
            if (!$email_sent) {
                error_log("Échec de l'envoi de l'email de notification de contact.");
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Votre message a été envoyé avec succès! Nous vous répondrons dans les plus brefs délais.'
            ]);
        } else {
            throw new Exception("Erreur lors de l'enregistrement du message.");
        }
        
    } catch (Exception $e) {
        error_log("Erreur contact form: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée.'
    ]);
}
?>
