<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Données POST reçues: " . print_r($_POST, true) . "\n", FILE_APPEND);

header('Content-Type: application/json');

require_once __DIR__ .'/../../config/auth.php';
require_once __DIR__ .'/../../config/DB.php';
require_once __DIR__ .'/../../config/functions.php';

if (!is_logged_in()) {
    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Utilisateur non connecté\n", FILE_APPEND);
    http_response_code(403);
    echo json_encode([
        'success' => false, 
        'message' => 'Accès non autorisé. Veuillez vous connecter.'
    ]);
    exit;
}

file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Utilisateur connecté: " . $_SESSION['user_id'] . "\n", FILE_APPEND);

$date = $_POST['date'] ?? null;
$time = $_POST['time'] ?? null;
$service = $_POST['service'] ?? null;
$notes = trim($_POST['notes'] ?? '');
$full_name = trim($_POST['fullName'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$user_id = $_SESSION['user_id'];

file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Données extraites:\n", FILE_APPEND);
file_put_contents('debug_log.txt', "Date: $date\n", FILE_APPEND);
file_put_contents('debug_log.txt', "Time: $time\n", FILE_APPEND);
file_put_contents('debug_log.txt', "Service: $service\n", FILE_APPEND);
file_put_contents('debug_log.txt', "Full Name: $full_name\n", FILE_APPEND);
file_put_contents('debug_log.txt', "Email: $email\n", FILE_APPEND);
file_put_contents('debug_log.txt', "Phone: $phone\n", FILE_APPEND);

$errors = [];

if (empty($date)) {
    $errors[] = 'La date est requise.';
}

if (empty($time)) {
    $errors[] = 'L\'heure est requise.';
}

if (empty($service)) {
    $errors[] = 'Le service est requis.';
}

if (empty($full_name)) {
    $errors[] = 'Le nom complet est requis.';
} elseif (strlen($full_name) < 2) {
    $errors[] = 'Le nom doit contenir au moins 2 caractères.';
}

if (empty($email)) {
    $errors[] = 'L\'email est requis.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'L\'adresse email n\'est pas valide.';
}

if (empty($phone)) {
    $errors[] = 'Le numéro de téléphone est requis.';
}
if (!empty($date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $errors[] = 'Le format de la date est invalide.';
}

if (!empty($time) && !preg_match('/^\d{2}:\d{2}$/', $time)) {
    $errors[] = 'Le format de l\'heure est invalide.';
}

if (!empty($errors)) {
    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Erreurs de validation: " . implode(', ', $errors) . "\n", FILE_APPEND);
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => implode(' ', $errors)
    ]);
    exit;
}

file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Validation réussie, tentative d'insertion\n", FILE_APPEND);

try {
    if (!isset($pdo)) {
        throw new Exception('Connexion à la base de données non établie');
    }
    
    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Connexion PDO OK\n", FILE_APPEND);
    $stmt = $pdo->prepare("SELECT id FROM appointments WHERE date = ? AND time = ?");
    $stmt->execute([$date, $time]);
    
    if ($stmt->fetch()) {
        file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Créneau déjà réservé\n", FILE_APPEND);
        http_response_code(409);
        echo json_encode([
            'success' => false, 
            'message' => 'Ce créneau vient d\'être réservé. Veuillez en choisir un autre.'
        ]);
        exit;
    }

    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Créneau disponible, insertion...\n", FILE_APPEND);
    $sql = "INSERT INTO appointments (user_id, full_name, email, phone, date, time, service, notes, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', NOW())";
    
    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - SQL: $sql\n", FILE_APPEND);
    
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        $user_id, 
        $full_name, 
        $email, 
        $phone, 
        $date, 
        $time, 
        $service, 
        $notes
    ]);

    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Résultat insertion: " . ($result ? 'SUCCESS' : 'FAILED') . "\n", FILE_APPEND);

    if ($result) {
        $appointment_id = $pdo->lastInsertId();

        $email_subject = "Confirmation de votre rendez-vous chez Mode Unique";
        $email_body = "
            <h1>Bonjour " . htmlspecialchars($full_name) . ",</h1>
            <p>Votre rendez-vous a bien été confirmé pour le <strong>" . date('d/m/Y', strtotime($date)) . " à " . htmlspecialchars($time) . "</strong>.</p>
            <p><strong>Service demandé :</strong> " . htmlspecialchars($service) . "</p>
            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            <p>Cordialement,<br>L'équipe Mode Unique</p>
        ";
        $email_sent = send_email($email, $email_subject, $email_body);
        if (!$email_sent) {
            error_log("Échec de l'envoi de l'email de confirmation de RDV à " . $email);
        }

        file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Rendez-vous créé avec ID: $appointment_id\n", FILE_APPEND);
        echo json_encode([
            'success' => true, 
            'message' => 'Rendez-vous enregistré avec succès.',
            'appointment_id' => $appointment_id
        ]);
    } else {
        $errorInfo = $stmt->errorInfo();
        file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Erreur SQL: " . print_r($errorInfo, true) . "\n", FILE_APPEND);
        throw new Exception('Échec de l\'insertion du rendez-vous: ' . print_r($errorInfo, true));
    }

} catch (PDOException $e) {
    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Erreur PDO: " . $e->getMessage() . "\n", FILE_APPEND);
    error_log("Erreur PDO process_appointment: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Une erreur de base de données est survenue: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Erreur générale: " . $e->getMessage() . "\n", FILE_APPEND);
    error_log("Erreur process_appointment: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Une erreur est survenue: ' . $e->getMessage()
    ]);
}
?>
