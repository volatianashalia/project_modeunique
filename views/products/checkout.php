<?php
session_start();
require_once('../layouts/header.php');
require_once '../../config/DB.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?redirect=checkout');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

$errors = $_SESSION['checkout_errors'] ?? [];
$saved_data = $_SESSION['checkout_data'] ?? [];
unset($_SESSION['checkout_errors'], $_SESSION['checkout_data']);
?>

<div class="container my-5">
    <h2 class="text-center mb-4">Finaliser ma commande</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white" style="background-color: #D4AF37 !important;">
                    <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Informations de livraison</h5>
                </div>
                <div class="card-body">
                    <form action="process_checkout.php" method="POST" id="checkoutForm">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="full_name" class="form-label">Nom complet *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?= htmlspecialchars($saved_data['full_name'] ?? ($user['first_name'] . ' ' . $user['last_name'])) ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($saved_data['email'] ?? $user['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= htmlspecialchars($saved_data['phone'] ?? $user['phone'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse *</label>
                            <input type="text" class="form-control" id="address" name="address" 
                                   value="<?= htmlspecialchars($saved_data['address'] ?? '') ?>" 
                                   placeholder="Numéro et nom de rue" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="city" class="form-label">Ville *</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?= htmlspecialchars($saved_data['city'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="postal_code" class="form-label">Code postal *</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                       value="<?= htmlspecialchars($saved_data['postal_code'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes de commande (optionnel)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Instructions spéciales, préférences de livraison..."><?= htmlspecialchars($saved_data['notes'] ?? '') ?></textarea>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Mode de paiement</h5>
                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="carte" 
                                       value="carte" <?= ($saved_data['payment_method'] ?? '') === 'carte' ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="carte">
                                    <i class="fas fa-credit-card me-2"></i>Carte bancaire
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" 
                                       value="paypal" <?= ($saved_data['payment_method'] ?? '') === 'paypal' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="paypal">
                                    <i class="fab fa-paypal me-2"></i>PayPal
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="virement" 
                                       value="virement" <?= ($saved_data['payment_method'] ?? '') === 'virement' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="virement">
                                    <i class="fas fa-university me-2"></i>Virement bancaire
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="especes" 
                                       value="especes" <?= ($saved_data['payment_method'] ?? '') === 'especes' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="especes">
                                    <i class="fas fa-money-bill-wave me-2"></i>Paiement en espèces à la livraison
                                </label>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                J'accepte les <a href="#" target="_blank">conditions générales de vente</a> *
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" 
                                style="background-color: #D4AF37; border-color: #D4AF37;">
                            <i class="fas fa-check-circle me-2"></i>Confirmer la commande
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Résumé de la commande</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                <small class="text-muted">Quantité: <?= $item['quantity'] ?></small>
                            </div>
                            <span class="fw-bold"><?= number_format($item['price'] * $item['quantity'], 2) ?> €</span>
                        </div>
                    <?php endforeach; ?>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sous-total:</span>
                        <strong><?= number_format($total, 2) ?> €</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frais de livraison:</span>
                        <span class="text-success">Gratuit</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>TVA (20%):</span>
                        <strong><?= number_format($total * 0.20, 2) ?> €</strong>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <h5>Total TTC:</h5>
                        <h5 class="text-primary" style="color: #D4AF37 !important;">
                            <?= number_format($total * 1.20, 2) ?> €
                        </h5>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="fas fa-info-circle me-2"></i>
                            Livraison estimée sous 3-5 jours ouvrables
                        </small>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="cart.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour au panier
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const terms = document.getElementById('terms');
    if (!terms.checked) {
        e.preventDefault();
        alert('Veuillez accepter les conditions générales de vente');
        return false;
    }
});
</script>
<?php include_once('../layouts/footer.php'); ?>
