<?php
session_start();
require_once __DIR__ '../../config/DB.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add_review.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
$review_text = trim(filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_SPECIAL_CHARS));

$errors = [];


if (!$rating || $rating < 1 || $rating > 5) {
    $errors[] = "Veuillez sélectionner une note valide.";
}
if (empty($review_text)) {
    $errors[] = "Veuillez écrire votre avis.";
}
if (strlen($review_text) < 10) {
    $errors[] = "Votre avis doit contenir au moins 10 caractères.";
}

if (!empty($errors)) {
    $_SESSION['review_errors'] = $errors;
    header('Location: add_review.php');
    exit();
}


try {
    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, rating, review_text) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $rating, $review_text]);

    $_SESSION['review_success'] = "Merci ! Votre avis a bien été soumis et sera visible après modération.";
    header('Location: add_review.php');
    exit();
} catch (Exception $e) {
    error_log("Erreur d'insertion d'avis: " . $e->getMessage());
    $_SESSION['review_errors'] = ["Une erreur est survenue. Veuillez réessayer."];
    header('Location: add_review.php');
    exit();
}
