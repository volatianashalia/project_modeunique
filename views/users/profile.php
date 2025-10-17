<?php
require_once __DIR__'../layouts/header.php';

if (!is_logged_in()) {
    header('Location: ../login.php?redirect=profile');
    exit();
}

$user = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (Exception $e) {
    error_log("Erreur de récupération du profil utilisateur: " . $e->getMessage());
}
?>

<div class="container" style="margin-top: 50px; max-width: 600px;">
    <h2 class="text-center mb-4">Mon profil</h2>
    <?php if (!empty($user)): ?>
        <div class="card">
            <div class="card-body">
                <p><strong>Nom :</strong> <?= htmlspecialchars($user['last_name']) ?></p>
                <p><strong>Prénom :</strong> <?= htmlspecialchars($user['first_name']) ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?></p>
                <p><strong>Statut :</strong> <?= htmlspecialchars($user['status']) ?></p>
                <p><strong>Dernière connexion :</strong> <?= htmlspecialchars($user['last_login']) ?></p>
                <a href="settings.php" class="btn btn-primary" style="background-color:#D4AF37; border:none;">Modifier mes informations</a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            Informations utilisateur introuvables.
        </div>
    <?php endif; ?>
    <div class="text-center mt-3">
        <a href="/ModeUnique/index.php" class="btn btn-secondary">Retour à l'accueil</a>
    </div>
</div>

<?php require_once __DIR__'../layouts/footer.php'; ?>
