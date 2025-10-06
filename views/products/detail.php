
<?php
require_once '../../config/DB.php';

$product = null;
$product_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($product_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
    } catch (Exception $e) {
        error_log("Erreur lors de la récupération du produit: " . $e->getMessage());
    }
}
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
    return number_format((float)$price, 2, ',', ' ') . 'Ar';
}


require_once('../layouts/header.php'); ?>

<div class="container" style="margin-top: 50px;">
    <?php if (!empty($product)): ?>
        <div class="row">
            <div class="col-md-6">
                <img 
                    src="<?= getImagePath($product['image'] ?? '') ?>" 
                    class="img-fluid rounded shadow-sm" 
                    alt="<?= htmlspecialchars($product['name'] ?? 'Produit') ?>" 
                    style="width: 100%; height: auto; max-height: 500px; object-fit: contain;"
                    onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZSBub24gZGlzcG9uaWJsZTwvdGV4dD48L3N2Zz4='">
            </div>
            <div class="col-md-6">
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                <p><strong>Prix :</strong> <?= number_format($product['price'], 2) ?>Ar</p>
                <p><strong>Stock :</strong> 
                    <?php if ($product['stock'] > 5): ?>
                        <span class="text-success">En stock</span>
                    <?php elseif ($product['stock'] > 0): ?>
                        <span class="text-warning">Stock faible (<?= $product['stock'] ?> restant(s))</span>
                    <?php else: ?>
                        <span class="text-danger">Rupture de stock</span>
                    <?php endif; ?>
                </p>
                <?php if (isset($_SESSION['user_id'])):?>
                    <?php if ($product['stock'] > 0): ?>
                    <form action="add_to_cart.php" method="post" class="mt-3">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantité</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="form-control" style="width:100px;" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="background-color:#D4AF37; border:none;">Ajouter au panier</button>
                    </form>
                    <?php else: ?>
                        <div class="alert alert-warning mt-3">Ce produit est en rupture de stock.</div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Vous devez être connecté pour ajouter des produits au panier.
                    </div>
                    <a href="/ModeUnique/views/login.php?redirect=views/products/detail.php?id=<?= $product['id'] ?>" class="btn btn-primary w-100" style="background-color:#D4AF37; border:none;">
                        Se connecter pour acheter
                    </a>
                <?php endif; ?>
                <a href="/ModeUnique/views/products/productPage.php" class="btn btn-secondary mt-3">Retour à la boutique</a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            Produit introuvable.
        </div>
    <?php endif; ?>
</div>

<?php include_once('../layouts/footer.php'); ?>