 
 <?php
require_once __DIR__'../../config/DB.php';
require_once __DIR__ '../../config/auth.php';
require_once __DIR__'../../config/functions.php';
secure_session_start();

if (!is_logged_in()) {
    header('Location: ../login.php');
    exit();
}

if (!is_admin()) {
    header('Location: ../../index.php');
    exit();
}
$csrf_token = generate_csrf_token();
$clients = [];
$orders = [];
$appointments = [];
$products = [];
$creations = [];
$contact_messages = [];
$new_messages_count = 0;
$reviews = [];
$pending_reviews_count = 0;
$stats = [];

try {

    $clientsStmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, status, created_at 
        FROM users 
        WHERE role = 'client' 
        ORDER BY created_at DESC
    ");
    $clientsStmt->execute();
    $clients = $clientsStmt->fetchAll();

$ordersStmt = $pdo->prepare("
    SELECT 
        o.id, 
        o.order_number,
        o.total_amount, 
        o.status,
        o.payment_method,
        o.created_at,
        u.first_name, 
        u.last_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 50
");
$ordersStmt->execute();
$orders = $ordersStmt->fetchAll();

    $appointmentsStmt = $pdo->prepare("
        SELECT a.id, a.date, a.time, a.service, a.notes, a.status,
               u.first_name, u.last_name, a.full_name
        FROM appointments a 
        LEFT JOIN users u ON a.user_id = u.id 
        ORDER BY a.date DESC, a.time DESC 
        LIMIT 20
    ");
    $appointmentsStmt->execute();
    $appointments = $appointmentsStmt->fetchAll();

    $productsStmt = $pdo->prepare("
        SELECT p.id, p.name, p.description, p.price, p.stock, p.image, p.size,
               c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC
    ");
    $productsStmt->execute();
    $products = $productsStmt->fetchAll();


    $creationsStmt = $pdo->prepare("
        SELECT id, title, description, image, created_at
        FROM creations 
        ORDER BY created_at DESC
    ");
    $creationsStmt->execute();
    $creations = $creationsStmt->fetchAll();


$categoriesStmt = $pdo->query("
    SELECT c.*, COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.id DESC
");
$categories = $categoriesStmt->fetchAll();

    $messagesStmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, subject, message, status, created_at
        FROM contact_messages
        ORDER BY created_at DESC
    ");
    $messagesStmt->execute();
    $contact_messages = $messagesStmt->fetchAll();


    $new_messages_count_stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'nouveau'");
    $new_messages_count = $new_messages_count_stmt->fetchColumn();

    $reviewsStmt = $pdo->prepare("
        SELECT r.id, r.user_id, r.rating, r.review_text, r.is_visible, r.created_at,
               u.first_name, u.last_name
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC
    ");
    $reviewsStmt->execute();
    $reviews = $reviewsStmt->fetchAll();

    $pending_reviews_count_stmt = $pdo->query("SELECT COUNT(*) FROM reviews WHERE is_visible = 0");
    $pending_reviews_count = $pending_reviews_count_stmt->fetchColumn();

    $statsStmt = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM users WHERE role = 'client') as client_count,
            (SELECT COUNT(*) FROM orders) as order_count,
            (SELECT COUNT(*) FROM appointments) as appointment_count,
            (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled') as total_revenue
    ");
    $stats = $statsStmt->fetch();


    function getImagePath($imagePath) { 
        if (empty($imagePath) || strpos($imagePath, 'http') === 0 || $imagePath[0] === '/') {
  
        return '../../' . ltrim($imagePath, '/');
    }
} catch (Exception $e) {
    error_log("Erreur admin panel: " . $e->getMessage());
    $error_message = "Erreur lors du chargement des données.";
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Mode Unique</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php if (isset($_GET['logout_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <strong>Erreur !</strong> Une erreur est survenue lors de la tentative de déconnexion. Vous êtes toujours connecté.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</head>
<body>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
               <img src="../../images/logo/Black_And_Gold-removebg-preview.png" alt="Mode Unique" style="width: 60%;height:60%;">
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="#" class="nav-link active" data-section="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="clients">
                        <i class="fas fa-users"></i>
                        <span id="client-count-sidebar">Clients (<?php echo $stats['client_count']; ?>)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="orders">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Commandes (<?php echo count($orders); ?>)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="appointments">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Rendez-vous (<?php echo count($appointments); ?>)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="products">
                        <i class="fas fa-box"></i>
                        <span>Produits (<?php echo count($products); ?>)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="creations">
                        <i class="fas fa-palette"></i>
                        <span>Créations (<?php echo count($creations); ?>)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="categories">
                        <i class="fas fa-tags"></i>
                        
                        <span>Catégories (<?php echo count($categories ?? []); ?>)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="reviews">
                        <i class="fas fa-star"></i>
                        <span>Avis</span>
                        <?php if ($pending_reviews_count > 0): ?>
                            <span class="badge bg-warning ms-2"><?= $pending_reviews_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer" style="margin-top:30px;">
            <a href="../../views/profile/settings.php" class="nav-link">
                <i class="fas fa-cog"></i>
                <span>Paramètres</span>
            </a>
            <form id="logoutForm" method="GET" action="logout_admin.php" style="margin: 0; padding: 0;">
        <button type="button" class="nav-link"
                onclick="if(confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) { document.getElementById('logoutForm').submit(); } return false;"
                style="background: none; border: none; width: 100%; text-align: left; cursor: pointer; color: inherit; padding: 15px 20px; display: flex; align-items: center; gap: 10px;"
                onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                onmouseout="this.style.backgroundColor='transparent'">
            <i class="fas fa-sign-out-alt"></i>
            <span>Se déconnecter</span>
        </button>
    </form>
        </div>
    </div>

    <div class="main-content">
        <header class="header">
            <div class="header-left">
                <h1 class="page-title" id="pageTitle">Tableau de bord</h1>
            </div>
            <div class="header-right">
                <div class="dropdown me-3">
                    <button class="btn btn-outline-secondary position-relative" type="button" id="messagesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-envelope"></i>
                        <?php if ($new_messages_count > 0): ?><span id="message-count-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $new_messages_count ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="messagesDropdown" style="width: 350px;">
                        <li class="dropdown-header">
                            Vous avez <?= $new_messages_count ?> nouveau(x) message(s)
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <div class="message-list" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach (array_slice($contact_messages, 0, 5) as $msg): ?>
                                <li>
                                    <a class="dropdown-item view-message <?= $msg['status'] === 'nouveau' ? 'fw-bold' : '' ?>" href="#" data-id="<?= $msg['id'] ?>" data-bs-toggle="modal" data-bs-target="#viewMessageModal">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-truncate" style="max-width: 200px;"><?= htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']) ?></span>
                                            <small class="text-muted"><?= date('d/m H:i', strtotime($msg['created_at'])) ?></small>
                                        </div>
                                        <div class="text-muted small text-truncate"><?= htmlspecialchars($msg['subject']) ?></div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </div>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#" data-section="messages" onclick="showSection('messages'); return false;">Voir tous les messages</a></li>
                    </ul>
                </div>
                <div class="user-profile">
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
                    <small class="text-muted">(Administrateur)</small>
                </div>
            </div>
        </header>

        <main class="content" id="mainContent">
            <!-- Dashboard Section -->
            <div class="section active" id="dashboard">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['client_count']; ?></h3>
                            <p>Clients inscrits</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['order_count']; ?></h3>
                            <p>Total commandes</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon bg-info">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['appointment_count']; ?></h3>
                            <p>Rendez-vous planifiés</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-euro-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo number_format($stats['total_revenue'], 2); ?> Ariary</h3>
                            <p>Chiffre d'affaires</p>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Commandes récentes</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>N° Commande</th>
                                                <th>Client</th>
                                                <th>Montant</th>
                                                <th>Statut</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($orders)): ?>
                                                <tr><td colspan="5" class="text-center text-muted">Aucune commande</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($orders as $order): ?>
                                                    <tr>
                                                        <td>#<?php echo $order['order_number']; ?></td>
                                                        <td><?php echo htmlspecialchars(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')); ?></td>
                                                        <td><?php echo number_format($order['total_amount'], 2); ?> Ariary</td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $order['status'] == 'completed' ? 'success' : ($order['status'] == 'pending' ? 'warning' : 'info'); ?>">
                                                                <?php echo htmlspecialchars($order['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Prochains rendez-vous</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($appointments)): ?>
                                    <p class="text-muted">Aucun rendez-vous planifié</p>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach (array_slice($appointments, 0, 5) as $appt): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($appt['service']); ?></h6>
                                                    <small><?php echo date('d/m', strtotime($appt['date'])); ?></small>
                                                </div>
                                                <p class="mb-1"><?php echo htmlspecialchars(($appt['first_name'] ?? $appt['full_name']) . ' ' . ($appt['last_name'] ?? '')); ?></p>
                                                <small><?php echo date('H:i', strtotime($appt['time'])); ?></small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section" id="products">
                <div class="section-header">
                    <h2>Gestion des produits</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus"></i> Ajouter nouveau produit
                    </button>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="productSearch" placeholder="Search products...">
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="categoryFilter">
                                    <option value="">Toutes les catégories</option>
                                    <option value="Dresses">Robes</option>
                                    <option value="Suits">Costumes</option>
                                    <option value="Tops">Hauts</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="availabilityFilter">
                                    <option value="">Toutes les disponibilités</option>
                                    <option value="Available">Disponible</option>
                                    <option value="Out of Stock">Rupture de stock</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row" id="productsGrid"> 
                            <?php foreach ($products as $product): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <img src="<?= getImagePath(htmlspecialchars($product['image'])) ?>" 
                                            class="card-img-top" 
                                            alt="<?= htmlspecialchars($product['name']) ?>" 
                                            style="height:200px; object-fit:cover;">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                            <p class="text-muted small"><?= htmlspecialchars($product['category_name'] ?? 'Non catégorisé') ?></p>
                                            
                                            <p class="fw-bold fs-5 text-primary mb-2">
                                                <?= number_format($product['price'], 2) ?> Ariary
                                            </p>
                                            
                                            <p class="mb-2">
                                                <span class="badge bg-<?= $product['stock'] > 0 ? 'success' : 'danger' ?>">
                                                    <?= $product['stock'] ?> in stock
                                                </span>
                                            </p>
                                            
                                            <p class="card-text small" style="max-height: 60px; overflow: hidden;">
                                                <?= htmlspecialchars(mb_strimwidth($product['description'], 0, 100, '...')) ?>
                                            </p>
                                        </div>
                                        <div class="card-footer text-end bg-transparent">
                                            <button class="btn btn-sm btn-warning editProduct" 
                                                    data-id="<?= $product['id'] ?>" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editProductModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger deleteProduct" 
                                                    data-id="<?= $product['id'] ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
      
                        </div>
                    </div>
                </div>
            </div>
            <div class="section" id="creations">
                <div class="section-header">
                    <h2>Gestion des créations</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCreationModal">
                        <i class="fas fa-plus"></i> Ajouter nouvelle creation
                        </button>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="creationSearch" placeholder="Search creations...">
                            </div>
                        </div>
                        
                        <div class="row" id="creationsGrid"> 
                            <!-- Dynamic content will be loaded here -->
                            <?php foreach ($creations as $creation): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <img src="<?= getImagePath(htmlspecialchars($creation['image'])) ?>" class="card-img-top" alt="<?= htmlspecialchars($creation['title']) ?>" style="height:200px; object-fit:cover;">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($creation['title']) ?></h5>
                                            <p class="card-text"><?= htmlspecialchars(mb_strimwidth($creation['description'], 0, 80, '...')) ?></p>
                                        </div>
                                        <div class="card-footer text-end">

                                            <button class="btn btn-sm btn-warning editCreation" 
                                                    data-id="<?= $creation['id'] ?>" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editCreationModal">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <button class="btn btn-sm btn-danger deleteCreation" 
                                                    data-id="<?= $creation['id'] ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section" id="categories">
                <div class="section-header">
                    <h2>Gestion des catégories</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus"></i> Ajouter nouvelle catégorie
                    </button>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="categorySearch" placeholder="Rechercher une catégorie...">
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom de la catégorie</th>
                                        <th>Nombre de produits</th>
                                        <th>Image</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="categoriesTable">
                                    <?php
                                    // Récupérer les catégories avec le nombre de produits
                                    $categoriesStmt = $pdo->query("
                                        SELECT c.*, 
                                            COUNT(p.id) as product_count 
                                        FROM categories c 
                                        LEFT JOIN products p ON c.id = p.category_id 
                                        GROUP BY c.id 
                                        ORDER BY c.id DESC
                                    ");
                                    $categories = $categoriesStmt->fetchAll();
                                    
                                    foreach ($categories as $category): 
                                    ?>
                                        <tr>
                                            <td><?= $category['id'] ?></td>
                                            <td><?= htmlspecialchars($category['Name']) ?></td>
                                            <td>
                                                <span class="badge bg-info"><?= $category['product_count'] ?> produits</span>
                                            </td>
                                             <td>
                                                <span class="badge bg-info"><img src="<?= getImagePath(htmlspecialchars($category['image'] ?? '')) ?>" alt="<?= htmlspecialchars($category['Name']) ?>" style="height:100px; object-fit:cover;">
</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning editCategory" 
                                                        data-id="<?= $category['id'] ?>"
                                                        data-name="<?= htmlspecialchars($category['Name']) ?>"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editCategoryModal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <?php if ($category['product_count'] == 0): ?>
                                                <button class="btn btn-sm btn-danger deleteCategory" 
                                                        data-id="<?= $category['id'] ?>"
                                                        data-name="<?= htmlspecialchars($category['Name']) ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                                <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled title="Impossible de supprimer une catégorie avec des produits">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section" id="reviews">
                <div class="section-header">
                    <h2>Gestion des avis clients</h2>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Note</th>
                                        <th>Avis</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="reviewsTable">
                                    <?php if (empty($reviews)): ?>
                                        <tr><td colspan="6" class="text-center text-muted py-4">Aucun avis pour le moment.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($reviews as $review): ?>
                                            <tr id="review-row-<?= $review['id'] ?>">
                                                <td>
                                                    <img src="<?= getImagePath($review['profile_image'] ?? 'images/default_avatar.png') ?>" alt="" class="rounded-circle me-2" width="30" height="30">
                                                    <?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?>
                                                </td>
                                                <td>
                                                    <span class="text-warning">
                                                        <?php for ($i = 0; $i < $review['rating']; $i++) echo '★'; ?>
                                                        <?php for ($i = $review['rating']; $i < 5; $i++) echo '☆'; ?>
                                                    </span>
                                                </td>
                                                <td class="text-truncate" style="max-width: 300px;"><?= htmlspecialchars($review['review_text']) ?></td>
                                                <td class="review-status">
                                                    <?php if ($review['is_visible']): ?>
                                                        <span class="badge bg-success">Approuvé</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">En attente</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($review['created_at'])) ?></td>
                                                <td>
                                                    <?php if (!$review['is_visible']): ?>
                                                        <button class="btn btn-sm btn-success approve-review" data-id="<?= $review['id'] ?>" title="Approuver">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-sm btn-danger delete-review" data-id="<?= $review['id'] ?>" title="Supprimer">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="section" id="messages">
                <div class="section-header">
                    <h2>Messages de Contact</h2>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Statut</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Sujet</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="messagesTable">
                                    <?php foreach ($contact_messages as $message): ?>
                                        <tr class="<?= $message['status'] === 'nouveau' ? 'table-light fw-bold' : '' ?>">
                                            <td>
                                                <?php if ($message['status'] === 'nouveau'): ?>
                                                    <span class="badge bg-primary">Nouveau</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Lu</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) ?></td>
                                            <td><a href="mailto:<?= htmlspecialchars($message['email']) ?>"><?= htmlspecialchars($message['email']) ?></a></td>
                                            <td><?= htmlspecialchars($message['subject']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($message['created_at'])) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info view-message" 
                                                        data-id="<?= $message['id'] ?>"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#viewMessageModal">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger delete-message" 
                                                        data-id="<?= $message['id'] ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($contact_messages)): ?>
                                        <tr><td colspan="6" class="text-center text-muted">Aucun message.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

<div class="section" id="orders">
    <div class="section-header">
        <h2>Gestion des commandes</h2>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="orderSearch" placeholder="Rechercher une commande...">
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="orderStatusFilter">
                        <option value="">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="processing">En traitement</option>
                        <option value="shipped">Expédiée</option>
                        <option value="delivered">Livrée</option>
                        <option value="cancelled">Annulée</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="date" class="form-control" id="orderDateFilter">
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Paiement</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTable">
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucune commande
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong>#<?= $order['id'] ?></strong></td>
                                    <td><?= htmlspecialchars(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?></td>
                                    <td><strong><?= number_format($order['total_amount'], 2) ?> Ariary</strong></td>
                                    <td>
                                        <?php
                                        $statusBadges = [
                                            'pending' => 'bg-warning',
                                            'processing' => 'bg-info',
                                            'shipped' => 'bg-primary',
                                            'delivered' => 'bg-success',
                                            'cancelled' => 'bg-danger'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'En attente',
                                            'processing' => 'En traitement',
                                            'shipped' => 'Expédiée',
                                            'delivered' => 'Livrée',
                                            'cancelled' => 'Annulée'
                                        ];
                                        $badgeClass = $statusBadges[$order['status']] ?? 'bg-secondary';
                                        $statusLabel = $statusLabels[$order['status']] ?? $order['status'];
                                        ?>
                                        <select class="form-select form-select-sm update-order-status" 
                                                data-order-id="<?= $order['id'] ?>" 
                                                data-current-status="<?= $order['status'] ?>"
                                                style="min-width: 150px;">
                                            <?php foreach ($statusLabels as $status_key => $label): ?>
                                                <option value="<?= $status_key ?>" <?= ($order['status'] === $status_key) ? 'selected' : '' ?>>
                                                    <?= $label ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?php
                                        $paymentLabels = [
                                            'carte' => 'Carte bancaire',
                                            'paypal' => 'PayPal',
                                            'virement' => 'Virement',
                                            'especes' => 'Espèces'
                                        ];
                                        echo $paymentLabels[$order['payment_method'] ?? ''] ?? 'N/A';
                                        ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="viewOrder(<?= $order['id'] ?>)" 
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
            

          <div class="section" id="clients">
                <div class="section-header">
                    <h2>Tous les clients</h2>
                     <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClientModal">
                        <i class="fas fa-plus"></i>Ajouter nouveau client
                    </button> 
                 </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="clientSearch" placeholder="Search clients...">
                            </div> 
                             <div class="col-md-6">
                                <select class="form-select" id="clientFilter">
                                    <option value="">Tous les status</option>
                                    <option value="Active">Active</option>
                                    <option value="VIP">VIP</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div> 
                         </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Date d'inscription</th> 
                                        <!-- <th>Actions</th> -->
                                    </tr>
                                </thead>
                                <tbody id="clientsTable"> 
                                    <!-- Dynamic content will be loaded here -->
                                         <?php foreach ($clients as $client): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($client['last_name'] . ' ' .$client['first_name']) ?></td>
                                            <td><?= htmlspecialchars($client['email']) ?></td>
                                            <td><?= htmlspecialchars($client['status']) ?></td>
                                            <td><?= htmlspecialchars($client['created_at']) ?></td> 
                                            <td>
                                                <a href="CRUD/edit_client.php?id=<?= $client['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                                                <a href="CRUD/delete_client.php?id=<?= $client['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce client ?')">Supprimer</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?> 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
<<<<<<< HEAD
            </div>
=======
            </div> 

>>>>>>> 0b730f7ae18d8c09840bb0cdb7340fc172ffe73e
             <div class="section" id="appointments">
                <div class="section-header">
                    <h2>Rendez-vous</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                        <i class="fas fa-plus"></i> Planifier un rendez-vous
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <input type="date" class="form-control" id="appointmentDateFilter">
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-select" id="appointmentTypeFilter">
                                            <option value="">Tous les types</option>
                                            <option value="Consultation">Consultation</option>
                                            <option value="Fitting">Essayage</option>
                                            <option value="Design Review">Revue de conception</option>
                                            <option value="Final Fitting">Essayage final</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                 <th>Date & Heure</th>
                                                <th>Client</th>
                                                <th>Service</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody id="appointmentsTable"> 
                                            <!-- Dynamic content will be loaded here -->
                                          <?php foreach ($appointments as $appt): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($appt['date']) ?> <?= htmlspecialchars($appt['time']) ?></td>
                                            <td><?= htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']) ?></td>
                                            <td><?= htmlspecialchars($appt['service']) ?></td>
                                            <td><?= htmlspecialchars($appt['notes']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Rendez-vous d'aujourd'hui</h5>
                            </div>
                            <div class="card-body">
                                <div id="todaySchedule">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
<<<<<<< HEAD

=======
     
>>>>>>> 0b730f7ae18d8c09840bb0cdb7340fc172ffe73e
            <div class="modal fade" id="addProductModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter nouveau produit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="addProductForm" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nom du produit</label>
                                            <input type="text" class="form-control" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Catégorie</label>
                                            <select class="form-select" name="category_id" required>
                                                <option value="">Choisir une catégorie</option>
                                                <?php 
                                                $categories = $pdo->query("SELECT id, name FROM categories")->fetchAll();
                                                foreach($categories as $cat): 
                                                ?>
                                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Prix (Ariary)</label>
                                            <input type="number" class="form-control" name="price" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Stock</label>
                                            <input type="number" class="form-control" name="stock" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Image du produit</label>
                                    <input type="file" class="form-control" name="image" accept="image/*" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tailles disponibles</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="sizes[]" value="XS" id="sizeXS">
                                            <label class="form-check-label" for="sizeXS">XS</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="sizes[]" value="S" id="sizeS">
                                            <label class="form-check-label" for="sizeS">S</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="sizes[]" value="M" id="sizeM">
                                            <label class="form-check-label" for="sizeM">M</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="sizes[]" value="L" id="sizeL">
                                            <label class="form-check-label" for="sizeL">L</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="sizes[]" value="XL" id="sizeXL">
                                            <label class="form-check-label" for="sizeXL">XL</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="sizes[]" value="XXL" id="sizeXXL">
                                            <label class="form-check-label" for="sizeXXL">XXL</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Ajouter le produit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

                <div class="modal fade" id="editProductModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Modifier le produit</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form id="editProductForm" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="id" id="editProductId">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Nom du produit</label>
                                                <input type="text" class="form-control" name="name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Catégorie</label>
                                                <select class="form-select" name="category_id" required>
                                                    <option value="">Choisir une catégorie</option>
                                                    <?php 
                                                    $categories = $pdo->query("SELECT id, name FROM categories")->fetchAll();
                                                    foreach($categories as $cat): 
                                                    ?>
                                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Prix (Ariary)</label>
                                                <input type="number" class="form-control" name="price" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Stock</label>
                                                <input type="number" class="form-control" name="stock" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3" required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Image du produit (optionnel)</label>
                                        <input type="file" class="form-control" name="image" accept="image/*">
                                        <small class="text-muted">Laissez vide pour conserver l'image actuelle</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Tailles disponibles</label>
                                        <div class="d-flex gap-3">
                                            <?php foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sizes[]" value="<?php echo $size; ?>" id="editSize<?php echo $size; ?>">
                                                <label class="form-check-label" for="editSize<?php echo $size; ?>"><?php echo $size; ?></label>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Modifier le produit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
<div class="modal fade" id="addCreationModal" tabindex="-1" aria-labelledby="addCreationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCreationModalLabel">Ajouter nouvelle création</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCreationForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="mb-3">
                        <label class="form-label">Titre de la création</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter la création</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCreationModal" tabindex="-1" aria-labelledby="editCreationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCreationModalLabel">Modifier la création</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCreationForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="id" id="editCreationId">
                    <div class="mb-3">
                        <label class="form-label">Titre de la création</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image (optionnel)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">Laissez vide pour conserver l'image actuelle</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Modifier la création</button>
                </div>
            </form>
        </div>
    </div>
</div>
               
            <div class="modal fade" id="addCategoryModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter nouvelle catégorie</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="addCategoryForm">
                            <div class="modal-body">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" required>
                                    <small class="form-text text-muted">Ex: Robes, Costumes, Accessoires...</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Image du produit</label>
                                    <input type="file" class="form-control" name="image" accept="image/*" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Ajouter la catégorie</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


         
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="id" id="editCategoryId">
                    
                    <div class="mb-3">
                        <label class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="editCategoryName" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image actuelle</label>
                        <div id="currentCategoryImage" class="mb-2">
                            <!-- L'image actuelle sera affichée ici -->
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nouvelle image (optionnel)</label>
                        <input type="file" class="form-control" name="image" id="editCategoryImageInput" accept="image/*">
                        <small class="text-muted">Laissez vide pour conserver l'image actuelle</small>
                    </div>
                    
                    <div class="mb-3">
                        <div id="imagePreview" style="display: none;">
                            <label class="form-label">Aperçu de la nouvelle image</label>
                            <img id="previewImage" src="" alt="Aperçu" style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Modifier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewMessageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>De :</strong> <span id="messageFromName"></span> (<span id="messageFromEmail"></span>)
                </div>
                <div class="mb-3">
                    <strong>Date :</strong> <span id="messageDate"></span>
                </div>
                <div class="mb-3">
                    <strong>Sujet :</strong> <span id="messageSubject"></span>
                </div>
                <hr>
                <div id="messageBody" style="white-space: pre-wrap; background-color: #f8f9fa; padding: 15px; border-radius: 5px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
        </main>
    </div>

    <script>
        window.csrfToken = '<?php echo $csrf_token; ?>';

        <?php
            $js_clients = [];
            foreach ($clients as $client) {
                $js_clients[] = [
                    'id' => $client['id'],
                    'name' => $client['first_name'] . ' ' . $client['last_name'],
                    'email' => $client['email'],
                    'status' => $client['status'],
                    'created_at' => $client['created_at']
                ];
            }
        ?>
        window.initialData = {
            clients: <?php echo json_encode($js_clients); ?>,
            orders: <?php echo json_encode($orders); ?>,
            appointments: <?php echo json_encode($appointments); ?>,
            products: <?php echo json_encode($products); ?>,
            creations: <?php echo json_encode($creations); ?>
        };
    </script>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script src="../../assets/js/admin.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page chargée');
    
    const logoutLink = document.querySelector('.logout-link');
    console.log('Lien trouvé:', logoutLink);
    
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            console.log('CLIC DÉTECTÉ !');
            console.log('Événement:', e);
            console.log('Lien href:', this.getAttribute('href'));
        }, true); // Le 'true' capture l'événement avant tout
    }
});
</script>
</body>
</html>
 
 
