<div class="container avis-client">
        <h2 class="mb-4">Ce que nos clients disent de nous</h2>
        <div class="row">
            <?php
            try {
               $stmt = $pdo->query("
                SELECT 
                    u.first_name, 
                    u.last_name, 
                    u.profile_image,
                    r.review_text,
                    r.rating,
                    r.created_at
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.is_visible = 1 
                ORDER BY RAND() 
                LIMIT 3
            ");
               $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($reviews)) {
                echo '<div class="col-12 text-center"><p class="text-muted">Aucun avis pour le moment.</p></div>';
            } else {
                foreach ($reviews as $review):
                    $client_name = htmlspecialchars($review['first_name'] . ' ' . substr($review['last_name'], 0, 1) . '.');
                    $client_image = !empty($review['profile_image']) ? htmlspecialchars($review['profile_image']) : 'images/default_avatar.png';
        ?>
            <div class="col-md-4 d-flex flex-column align-items-center mb-4 text-center">
                <img src="<?= $client_image ?>" 
                     alt="Avis de <?= $client_name ?>" 
                     class="mb-3" 
                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                <div class="text-center">
                    <strong class="d-block mb-2"><?= $client_name ?></strong>
                    <div class="mb-2 text-warning">
                        <?php 
                        for ($i = 0; $i < $review['rating']; $i++) echo '★';
                        for ($i = $review['rating']; $i < 5; $i++) echo '☆';
                        ?>
                    </div>
                    <p class="avis-text">« <?= htmlspecialchars($review['review_text']) ?> »</p>
                </div>
            </div>
        <?php 
                endforeach;
            }
        } catch (Exception $e) {
            error_log("Erreur de récupération des avis: " . $e->getMessage());
            echo '<div class="col-12 text-center"><p class="text-danger">Erreur lors du chargement des avis.</p></div>';
        }
        ?>
    </div>
</div>