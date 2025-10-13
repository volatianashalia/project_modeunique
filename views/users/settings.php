<?php 
require_once __DIR__'../layouts/header.php'; 

if (!is_logged_in()) {
    header('Location: ../login.php?redirect=settings');
    exit();
}

$user = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (Exception $e) {
    error_log("Erreur de récupération des informations pour la page settings: " . $e->getMessage());
}
?>

<div class="container" style="margin-top: 50px; max-width: 600px;">
    <h2 class="text-center mb-4">Modifier mes informations</h2>
    <?php if (!empty($user)): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5>Photo de profil</h5>
            </div>
            <div class="card-body text-center">
                <?php
                $avatar = !empty($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'images/default_avatar.png';
                ?>
                <img src="../../<?= htmlspecialchars($avatar) ?>" 
                    alt="Photo de profil" 
                    class="rounded-circle mb-3" 
                    style="width: 150px; height: 150px; object-fit: cover;">
                
                <form action="upload_avatar.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="file" class="form-control" name="profile_image" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Changer la photo</button>
                </form>
            </div>
        </div>
        <form action="update_settings.php" method="post">
            <div class="mb-3">
                <label for="last_name" class="form-label">Nom</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="first_name" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" class="form-control" id="password" name="password" minlength="8">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-success" style="background-color:#D4AF37; border:none;">Enregistrer</button>
                <a href="profile.php" class="btn btn-secondary ms-2">Annuler</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            Impossible de charger les informations utilisateur.
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__'../layouts/footer.php'; ?>
