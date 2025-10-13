<?php 
require_once __DIR__ '../../config/DB.php';
function getImagePath($imagePath) {
    if (empty($imagePath) || trim($imagePath) === '') {
        return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZSBub24gZGlzcG9uaWJsZTwvdGV4dD48L3N2Zz4=';
    }
    if (strpos($imagePath, 'http') === 0 || strpos($imagePath, '/') === 0) {
        return $imagePath;
    }
    return '../../' . ltrim($imagePath, '/');
}
function formatPrice($price) {
    return number_format((float)$price, 2, ',', ' ') . ' Ar';
}
?>
<div class="container" style="margin-top: 50px;">
    <h2 class="text-center mb-4 " style="color:#D4AF37;">Boutique - Prêt-à-porter</h2>
    
    <?php if (!empty($products) && is_array($products)): ?>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-img-container" style="height:250px; overflow:hidden;">
                            <img 
                                src="<?= getImagePath($product['image'] ?? '') ?>" 
                                class="card-img-top" 
                                alt="<?= htmlspecialchars($product['name'] ?? 'Produit') ?>" 
                                style="height:100%; width:100%; object-fit:cover;"
                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZSBub24gZGlzcG9uaWJsZTwvdGV4dD48L3N2Zz4='"
                            >
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($product['name'] ?? 'Produit sans nom') ?></h5>
                            <p class="card-text text-muted flex-grow-1">
                                <?= htmlspecialchars(mb_strimwidth($product['description'] ?? 'Aucune description', 0, 100, '...')) ?>
                            </p>
                            <div class="mt-auto">
                                <p class="card-text">
                                    <strong class="text-primary"><?= formatPrice($product['price'] ?? 0) ?></strong>
                                </p>
                                <?php if (isset($product['stock']) && $product['stock'] > 0): ?>
                                    <small class="text-success">En stock (<?= $product['stock'] ?>)</small>
                                <?php else: ?>
                                    <small class="text-danger">Rupture de stock</small>
                                <?php endif; ?>
                                <div class="mt-2">
                                    <a href="detail.php?id=<?= $product['id'] ?? 0 ?>" 
                                       class="btn btn-primary w-100" 
                                       style="background-color:#D4AF37; border-color:#D4AF37;">
                                        Voir le produit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            Aucun produit trouvé pour le moment.
        </div>
        <div class="text-center">
            <a href="../../index.php" class="btn btn-primary">Retour à l'accueil</a>
        </div>
    <?php endif; ?>
</div>
