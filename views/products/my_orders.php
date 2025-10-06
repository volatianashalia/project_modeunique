<?php
session_start();
require_once('../layouts/header.php');
require_once '../../config/DB.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?redirect=my_orders');
    exit();
}

$user_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            o.order_number,
            o.total_amount,
            o.status,
            o.payment_method,
            o.payment_status,
            o.created_at,
            COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();

} catch (Exception $e) {
    error_log("Erreur lors de la récupération des commandes: " . $e->getMessage());
    $orders = [];
}
function getStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge bg-warning">En attente</span>',
        'processing' => '<span class="badge bg-info">En traitement</span>',
        'shipped' => '<span class="badge bg-primary">Expédiée</span>',
        'delivered' => '<span class="badge bg-success">Livrée</span>',
        'cancelled' => '<span class="badge bg-danger">Annulée</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">Inconnu</span>';
}
function getPaymentBadge($status) {
    $badges = [
        'pending' => '<span class="badge bg-warning text-dark">En attente</span>',
        'paid' => '<span class="badge bg-success">Payé</span>',
        'failed' => '<span class="badge bg-danger">Échoué</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">Inconnu</span>';
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-shopping-bag me-2"></i>Mes commandes</h2>
                <a href="productPage.php" class="btn btn-primary" style="background-color: #D4AF37; border-color: #D4AF37;">
                    <i class="fas fa-store me-2"></i>Continuer mes achats
                </a>
            </div>

            <?php if (empty($orders)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                    <h4>Aucune commande</h4>
                    <p>Vous n'avez pas encore passé de commande.</p>
                    <a href="productPage.php" class="btn btn-primary mt-3" style="background-color: #D4AF37; border-color: #D4AF37;">
                        Découvrir nos produits
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($orders as $order): ?>
                        <div class="col-12 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <small class="text-muted">Numéro de commande</small><br>
                                            <strong><?= htmlspecialchars($order['order_number']) ?></strong>
                                        </div>
                                        <div class="col-md-2">
                                            <small class="text-muted">Date</small><br>
                                            <strong><?= date('d/m/Y', strtotime($order['created_at'])) ?></strong>
                                        </div>
                                        <div class="col-md-2">
                                            <small class="text-muted">Total</small><br>
                                            <strong class="text-primary"><?= number_format($order['total_amount'], 2) ?> €</strong>
                                        </div>
                                        <div class="col-md-2">
                                            <small class="text-muted">Statut</small><br>
                                            <?= getStatusBadge($order['status']) ?>
                                        </div>
                                        <div class="col-md-2">
                                            <small class="text-muted">Paiement</small><br>
                                            <?= getPaymentBadge($order['payment_status']) ?>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#order-<?= $order['id'] ?>">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="collapse" id="order-<?= $order['id'] ?>">
                                    <div class="card-body">
                                        <?php
                                        $itemStmt = $pdo->prepare("
                                            SELECT * FROM order_items 
                                            WHERE order_id = ?
                                        ");
                                        $itemStmt->execute([$order['id']]);
                                        $items = $itemStmt->fetchAll();
                                        ?>

                                        <h6 class="mb-3">Articles commandés (<?= count($items) ?>)</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
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
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                        <td class="text-end"><strong class="text-primary"><?= number_format($order['total_amount'], 2) ?> €</strong></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <h6>Informations de paiement</h6>
                                                <p class="mb-1">
                                                    <small class="text-muted">Méthode:</small>
                                                    <?php
                                                    $payment_methods = [
                                                        'carte' => 'Carte bancaire',
                                                        'paypal' => 'PayPal',
                                                        'virement' => 'Virement bancaire',
                                                        'especes' => 'Espèces à la livraison'
                                                    ];
                                                    echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                                                    ?>
                                                </p>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <a href="order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-eye me-1"></i>Voir détails
                                                </a>
                                                <?php if ($order['status'] === 'pending'): ?>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="cancelOrder(<?= $order['id'] ?>, '<?= htmlspecialchars($order['order_number']) ?>')">
                                                        <i class="fas fa-times me-1"></i>Annuler
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <span class="page-link">Total: <?= count($orders) ?> commande(s)</span>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function cancelOrder(orderId, orderNumber) {
    if (confirm('Êtes-vous sûr de vouloir annuler la commande ' + orderNumber + ' ?')) {
        fetch('cancel_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'order_id=' + orderId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Commande annulée avec succès');
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
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