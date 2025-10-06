<?php
session_start();
require_once '../../config/DB.php';
require_once '../../config/auth.php';
require_once '../../config/functions.php';

// Vérifier si l'utilisateur est connecté
if (!is_logged_in()) {
    header('Location: ../login.php');
    exit();
}

// Vérifier que la méthode est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: settings.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];

// Récupérer et nettoyer les données
$first_name = sanitize($_POST['first_name'] ?? '');
$last_name = sanitize($_POST['last_name'] ?? '');
$new_password = $_POST['password'] ?? ''; // Le mot de passe n'est pas nettoyé ici car il sera haché

// Validation
if (empty($first_name)) {
    $errors[] = "Le prénom est requis.";
}
if (empty($last_name)) {
    $errors[] = "Le nom est requis.";
}

$sql_parts = [];
$params = [];

$sql_parts[] = "first_name = ?";
$params[] = $first_name;

$sql_parts[] = "last_name = ?";
$params[] = $last_name;

// Si un nouveau mot de passe est fourni, le valider et le hacher
if (!empty($new_password)) {
    $password_errors = validate_password($new_password);
    if (!empty($password_errors)) {
        $errors = array_merge($errors, $password_errors);
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_ARGON2ID);
        $sql_parts[] = "password = ?";
        $params[] = $hashed_password;
    }
}

if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header('Location: settings.php');
    exit();
}

try {
    $sql = "UPDATE users SET " . implode(', ', $sql_parts) . " WHERE id = ?";
    $params[] = $user_id;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Mettre à jour les informations de session si le nom ou prénom a changé
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;

    $_SESSION['success'] = "Vos informations ont été mises à jour avec succès.";
    header('Location: settings.php');
    exit();
} catch (Exception $e) {
    error_log("Erreur de mise à jour des paramètres utilisateur: " . $e->getMessage());
    $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour. Veuillez réessayer.";
    header('Location: settings.php');
    exit();
}
?>