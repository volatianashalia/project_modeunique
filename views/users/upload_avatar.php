<?php
require_once '../../config/DB.php';
require_once '../../config/auth.php';
require_once '../../config/functions.php';

secure_session_start();

if (!is_logged_in()) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    try {
        $file = $_FILES['profile_image'];
        
        // Validation
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception('Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.');
        }
        
        if ($file['size'] > $max_size) {
            throw new Exception('Le fichier est trop volumineux. Maximum 5MB.');
        }
        
        // Créer le dossier uploads/avatars s'il n'existe pas
        $upload_dir = '../../uploads/avatars/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Générer un nom unique pour le fichier
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Supprimer l'ancienne image si elle existe
            $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $old_image = $stmt->fetchColumn();
            
            if ($old_image && file_exists('../../' . $old_image)) {
                unlink('../../' . $old_image);
            }
            
            // Mettre à jour la base de données
            $db_path = 'uploads/avatars/' . $filename;
            $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt->execute([$db_path, $_SESSION['user_id']]);
            
            $_SESSION['success'] = 'Photo de profil mise à jour avec succès !';
            header('Location: settings.php');
            exit();
        } else {
            throw new Exception('Erreur lors du téléchargement du fichier.');
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: settings.php');
        exit();
    }
}
?>