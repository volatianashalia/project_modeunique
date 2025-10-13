<?php
session_start();
require_once __DIR__'../layouts/header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?redirect=add_review');
    exit();
}

// Récupérer les messages d'erreur ou de succès
$errors = $_SESSION['review_errors'] ?? [];
$success = $_SESSION['review_success'] ?? null;
unset($_SESSION['review_errors'], $_SESSION['review_success']);
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white" style="background-color: #D4AF37 !important;">
                    <h4 class="mb-0"><i class="fas fa-star me-2"></i>Laissez-nous votre avis</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($success) ?>
                            <br><a href="../../index.php">Retour à l'accueil</a>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <p class="text-muted">Votre opinion est précieuse pour nous aider à nous améliorer. Merci de prendre un moment pour partager votre expérience.</p>

                        <form action="process_review.php" method="POST">
                            <div class="mb-3">
                                <label for="rating" class="form-label">Votre note (sur 5)</label>
                                <select name="rating" id="rating" class="form-select" required>
                                    <option value="5">★★★★★ (Excellent)</option>
                                    <option value="4">★★★★☆ (Très bien)</option>
                                    <option value="3">★★★☆☆ (Moyen)</option>
                                    <option value="2">★★☆☆☆ (Insuffisant)</option>
                                    <option value="1">★☆☆☆☆ (Mauvais)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="review_text" class="form-label">Votre avis</label>
                                <textarea name="review_text" id="review_text" rows="5" class="form-control" placeholder="Racontez-nous votre expérience..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" style="background-color: #D4AF37; border-color: #D4AF37;">Envoyer mon avis</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__'../layouts/footer.php'; ?>
