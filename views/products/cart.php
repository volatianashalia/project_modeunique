<?php
session_start();
function getImagePath($imagePath) {
    if (empty($imagePath) || trim($imagePath) === '') {
        return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjZWVlIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlPC90ZXh0Pjwvc3ZnPg==';
    }
    
    if (strpos($imagePath, 'http') === 0 || strpos($imagePath, '/') === 0) {
        return $imagePath;
    }
    return '../../' . $imagePath;
}

require_once __DIR__'../layouts/header.php'; 
?>

<div class="container" style="margin-top: 50px;">
    <h2 class="text-center mb-4">Mon panier</h2>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php if ($_GET['success'] == 'added'): ?>
                Produit ajouté au panier avec succès !
            <?php elseif ($_GET['success'] == 'removed'): ?>
                Produit supprimé du panier.
            <?php elseif ($_GET['success'] == 'updated'): ?>
                Quantité mise à jour.
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php
    $cart = $_SESSION['cart'] ?? [];
    ?>
    
    <?php if (!empty($cart)): ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <?php
                        $grandTotal = 0;
                        foreach ($cart as $index => $item):
                            $total = $item['price'] * $item['quantity'];
                            $grandTotal += $total;
                        ?>
                            <div class="row align-items-center border-bottom py-3">
                                <div class="col-md-2">
                                    <img src="<?= getImagePath($item['image'] ?? '') ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>" 
                                         class="img-fluid rounded" 
                                         style="max-height: 80px; object-fit: cover;">
                                </div>
                                <div class="col-md-4">
                                    <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                    <small class="text-muted">Prix unitaire: <?= number_format($item['price'], 2) ?> €</small>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group" style="max-width: 130px;">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity(<?= $index ?>, -1)">-</button>
                                        <input type="number" class="form-control form-control-sm text-center" 
                                               value="<?= $item['quantity'] ?>" 
                                               min="1" 
                                               id="qty-<?= $index ?>"
                                               onchange="updateQuantity(<?= $index ?>, 0)">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity(<?= $index ?>, 1)">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <strong><?= number_format($total, 2) ?> €</strong>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-outline-danger btn-sm" onclick="removeItem(<?= $index ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Résumé de la commande</h5>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Sous-total:</span>
                            <strong><?= number_format($grandTotal, 2) ?> €</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Livraison:</span>
                            <span>Gratuite</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong class="text-primary"><?= number_format($grandTotal, 2) ?> €</strong>
                        </div>
                        <div class="mt-3">
                            <a href="checkout.php" class="btn btn-primary w-100" style="background-color:#D4AF37; border-color:#D4AF37;">
                                Passer à la caisse
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
            <h4>Votre panier est vide</h4>
            <p>Découvrez nos produits et ajoutez-les à votre panier.</p>
        </div>
    <?php endif; ?>
    
    <div class="text-center mt-4">
        <a href="productPage.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Continuer mes achats
        </a>
        <?php if (!empty($cart)): ?>
            <a href="clear_cart.php" class="btn btn-outline-danger ms-2" onclick="return confirm('Êtes-vous sûr de vouloir vider votre panier ?')">
                <i class="fas fa-trash me-2"></i>Vider le panier
            </a>
        <?php endif; ?>
    </div>
</div>

<script>
function updateQuantity(index, change) {
    const qtyInput = document.getElementById('qty-' + index);
    let currentQty = parseInt(qtyInput.value);
    
    if (change === 0) {
        currentQty = parseInt(qtyInput.value);
    } else {
        currentQty += change;
    }
    
    if (currentQty < 1) {
        currentQty = 1;
    }
    
    qtyInput.value = currentQty;
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('index', index);
    formData.append('quantity', currentQty);
    
    fetch('update_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la mise à jour: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue');
    });
}

function removeItem(index) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('index', index);
        
        fetch('update_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la suppression: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        });
    }
}
</script>

<?php include_once('../layouts/footer.php'); ?>
