<?php
require_once('../../../config/DB.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $location = trim($_POST['location']);
    $preferences = trim($_POST['preferences']);

    // Séparer nom et prénom
    $parts = explode(' ', $name, 2);
    $last_name = $parts[0];
    $first_name = isset($parts[1]) ? $parts[1] : '';

    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, location, preferences, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'client', 'Active', NOW())");
    $stmt->execute([$first_name, $last_name, $email, $phone, $location, $preferences]);

    header('Location: admin.php#clients');
    exit;
}