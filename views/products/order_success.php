<?php
session_start();
if (!isset($_SESSION['order_success'])) {
    header('Location: productPage.php');
    exit();
}
require_once('../layouts/header.php');
$order_data = $_SESSION['order_success'];
unset($_SESSION['order_success']);
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success shadow-lg">
                <div class="card-header bg-success text-white text-center py-4">
                    <i class="fas fa-check-circle fa-4x mb-3"></i>
                    <h2 class="mb-0">Commande confirmée !</h2>
                </div>
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h4 class="text-success">Merci pour votre commande !</h4>
                        <p class="lead">Votre commande a été enregistrée avec succès.</p>
                    </div>

                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-hashtag me-2"></i>Numéro de commande:</strong><br>
                                <span class="fs-5"><?= htmlspecialchars($order_data['order_number']) ?></span>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-euro-sign me-2"></i>Montant total:</strong><br>
                                <span class="fs-5"><?= number_format($order_data['total'], 2) ?> €</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-4 rounded mb-4">
                        <h5><i class="fas fa-envelope me-2"></i>Confirmation par email</h5>
                        <p class="mb-0">
                            Un email de confirmation a été envoyé à <strong><?= htmlspecialchars($order_data['email']) ?></strong>
                        </p>
                    </div>

                    <div class="row text-center mb-4">
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="fas fa-box fa-3x text-primary mb-3" style="color: #D4AF37 !important;"></i>
                                <h6>Préparation</h6>
                                <small class="text-muted">Votre commande est en cours de préparation</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                <h6>Expédition</h6>
                                <small class="text-muted">Vous serez notifié de l'expédition</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="fas fa-home fa-3x text-muted mb-3"></i>
                                <h6>Livraison</h6>
                                <small class="text-muted">Livraison sous 3-5 jours</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row text-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <a href="my_orders.php" class="btn btn-outline-primary btn-lg w-100">
                                <i class="fas fa-list me-2"></i>Mes commandes
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="productPage.php" class="btn btn-primary btn-lg w-100" 
                               style="background-color: #D4AF37; border-color: #D4AF37;">
                                <i class="fas fa-shopping-bag me-2"></i>Continuer mes achats
                            </a>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="fas fa-question-circle me-2"></i>
                            Des questions ? <a href="../appointments/contact.php">Contactez-nous</a>
                        </small>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <div class="alert alert-light">
                    <i class="fas fa-star text-warning"></i>
                    <i class="fas fa-star text-warning"></i>
                    <i class="fas fa-star text-warning"></i>
                    <i class="fas fa-star text-warning"></i>
                    <i class="fas fa-star text-warning"></i>
                    <p class="mb-0 mt-2">
                        <strong>Merci de votre confiance !</strong><br>
                        <small>L'équipe Mode Unique</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('../layouts/footer.php'); ?>