<?php
session_start();
require_once __DIR__'../layouts/header.php';
require_once __DIR__ '../../config/DB.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
$order_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$order_id) {
    header('Location: my_orders.php');
    exit();
}

$user_id = $_SESSION['user_id'];

try {

    $stmt = $pdo->prepare("
        SELECT * FROM orders 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();

    if (!$order) {
        header('Location: my_orders.php?error=not_found');
        exit();
    }
    $itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $itemStmt->execute([$order_id]);
    $items = $itemStmt->fetchAll();

} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    header('Location: my_orders.php?error=system');
    exit();
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <a href="my_orders.php" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left me-2"></i>Retour à mes commandes
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white" style="background-color: #D4AF37 !important;">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>
                        Commande #<?= htmlspecialchars($order['order_number']) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Informations de livraison</h6>
                            <p class="mb-1"><strong><?= htmlspecialchars($order['full_name']) ?></strong></p>
                            <p class="mb-1"><?= htmlspecialchars($order['address']) ?></p>
                            <p class="mb-1"><?= htmlspecialchars($order['postal_code']) ?> <?= htmlspecialchars($order['city']) ?></p>
                            <p class="mb-1"><i class="fas fa-phone me-2"></i><?= htmlspecialchars($order['phone']) ?></p>
                            <p class="mb-0"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($order['email']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Détails de la commande</h6>
                            <p class="mb-1">
                                <strong>Date:</strong> 
                                <?= date('d/m/Y à H:i', strtotime($order['created_at'])) ?>
                            </p>
                            <p class="mb-1">
                                <strong>Statut:</strong> 
                                <?php
                                $status_labels = [
                                    'pending' => '<span class="badge bg-warning">En attente</span>',
                                    'processing' => '<span class="badge bg-info">En traitement</span>',
                                    'shipped' => '<span class="badge bg-primary">Expédiée</span>',
                                    'delivered' => '<span class="badge bg-success">Livrée</span>',
                                    'cancelled' => '<span class="badge bg-danger">Annulée</span>'
                                ];
                                echo $status_labels[$order['status']] ?? $order['status'];
                                ?>
                            </p>
                            <p class="mb-1">
                                <strong>Paiement:</strong>
                                <?php
                                $payment_labels = [
                                    'carte' => 'Carte bancaire',
                                    'paypal' => 'PayPal',
                                    'virement' => 'Virement bancaire',
                                    'especes' => 'Espèces'
                                ];
                                echo $payment_labels[$order['payment_method']] ?? $order['payment_method'];
                                ?>
                            </p>
                            <p class="mb-0">
                                <strong>Statut paiement:</strong>
                                <?php
                                $payment_status = [
                                    'pending' => '<span class="badge bg-warning text-dark">En attente</span>',
                                    'paid' => '<span class="badge bg-success">Payé</span>',
                                    'failed' => '<span class="badge bg-danger">Échoué</span>'
                                ];
                                echo $payment_status[$order['payment_status']] ?? $order['payment_status'];
                                ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($order['notes'])): ?>
                        <div class="alert alert-light">
                            <strong>Notes:</strong> <?= nl2br(htmlspecialchars($order['notes'])) ?>
                        </div>
                    <?php endif; ?>

                    <h6 class="mt-4 mb-3">Articles commandés</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-end">Prix unitaire</th>
                                    <th class="text-end">Sous-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td class="text-center"><?= $item['quantity'] ?></td>
                                        <td class="text-end"><?= number_format($item['price'], 2) ?> €</td>
                                        <td class="text-end"><strong><?= number_format($item['subtotal'], 2) ?> €</strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Résumé</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sous-total:</span>
                        <strong><?= number_format($order['total_amount'], 2) ?> €</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Livraison:</span>
                        <span class="text-success">Gratuite</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong class="text-primary" style="color: #D4AF37 !important;">
                            <?= number_format($order['total_amount'], 2) ?> €
                        </strong>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Actions</h6>
                    <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimer
                    </button>
                    <?php if ($order['status'] === 'pending'): ?>
                        <button class="btn btn-outline-danger btn-sm w-100" 
                                onclick="cancelOrder(<?= $order['id'] ?>, '<?= htmlspecialchars($order['order_number']) ?>')">
                            <i class="fas fa-times me-2"></i>Annuler la commande
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelOrder(orderId, orderNumber) {
    if (confirm('Êtes-vous sûr de vouloir annuler la commande ' + orderNumber + ' ?')) {
        window.location.href = 'cancel_order.php?id=' + orderId;
    }
}
</script>

<?php require_once __DIR__'../layouts/footer.php'; ?>
