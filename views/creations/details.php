<?php
require_once __DIR__ '../../config/DB.php';
require_once __DIR__'../../config/functions.php';
$creation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$creation = null;

if ($creation_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM creations WHERE id = ?");
        $stmt->execute([$creation_id]);
        $creation = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de la création: " . $e->getMessage());
    }
}

require_once __DIR__'../layouts/header.php'; ?>

<div class="container" style="margin-top: 50px;">
    <?php if (!empty($creation)): ?>
        <div class="card mb-4">
            <img src="/ModeUnique/<?= htmlspecialchars($creation['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($creation['title']) ?>" style="max-width:400px; margin:auto;">
            <div class="card-body">
                <h2 class="card-title"><?= htmlspecialchars($creation['title']) ?></h2>
                <p class="card-text"><?= nl2br(htmlspecialchars($creation['description'])) ?></p>
                <p class="text-muted">Créé le : <?= htmlspecialchars(date('d/m/Y', strtotime($creation['created_at']))) ?></p>
                <a href="/ModeUnique/views/creations/creationPage.php" class="btn btn-primary" style="background-color:#D4AF37; border:none;">Retour aux créations</a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            Création introuvable.
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__'../layouts/footer.php'; ?>
