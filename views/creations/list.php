<?php
try {
    $stmt = $pdo->prepare("SELECT id, title, description, image FROM creations ORDER BY created_at DESC");
    $stmt->execute();
    $creations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des créations: " . $e->getMessage());
    $creations = [];
    echo '<div class="alert alert-danger">Impossible de charger les créations.</div>';
}
?>

<div class="container" style="margin-top: 20px;">
    <?php if (!empty($creations)): ?>
        <div class="row">
            <?php foreach ($creations as $creation): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($creation['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($creation['title']) ?>" style="height:250px;  width:407px; object-fit:cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($creation['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(mb_strimwidth($creation['description'], 0, 100, '...')) ?></p>
                            <a href="views/creations/details.php?id=<?= $creation['id'] ?>" class="btn btn-primary" style="background-color:#D4AF37; border:none;">Voir détails</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Aucune création trouvée.
        </div>
    <?php endif; ?>
</div>
