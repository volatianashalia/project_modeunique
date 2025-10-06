
 <div class="container category" style="margin-top: 5%; text-align: center;">
        <h2 style="margin-bottom: 3%; color:#D4AF37;">Nos catégories de créations:</h2>
        <p>Nos créations se déclinent en collections soigneusement structurées, pensées pour répondre aux attentes d’une clientèle exigeante. Chaque catégorie met en valeur un savoir-faire maîtrisé, des finitions irréprochables et une identité visuelle cohérente. Explorez nos univers et découvrez des pièces conçues pour allier esthétisme, confort et excellence.</p>
            <div class="row" style="margin-top:3%;">
            <?php
            $stmt = $pdo->query("SELECT * FROM categories");
            $categories = $stmt->fetchAll();

            foreach ($categories as $category): 
            ?>
                <div class="col-md-3" >
                    <div class="filter col" data-category="<?= htmlspecialchars($category['Name']) ?>">
                        <div class="card card-cover overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('<?= htmlspecialchars($category['image']) ?>');background-size:cover; background-position: center;background-repeat: no-repeat; height: 300px;width: 80%;">
                            <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                <h3 class="pt-5 mt-5 mb-4 display-8 lh-1 fw-bold"><?= htmlspecialchars($category['Name']) ?></h3>
                                <a href="views/products/productPage.php?category_id=<?= $category['id'] ?>" class="see btn" style="color:white;border: 1px solid #D4AF37 ;background-color: #D4AF37 ;border-radius:5%;padding: 2%; text-decoration: none;">Voir</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
    </div>