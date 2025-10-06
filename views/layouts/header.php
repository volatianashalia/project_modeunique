<?php
require_once __DIR__ .'/../../config/DB.php';
require_once __DIR__ .'/../../config/auth.php';
require_once __DIR__ .'/../../config/functions.php';

$errors = [];

    $cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'] ?? 0;
    }
}
    $isLoggedIn = isset($_SESSION['user_id']);
    $userName = $isLoggedIn ? ($_SESSION['first_name'] ?? 'User') : '';
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <title>Mode Unique</title>
    <link rel="stylesheet" href="../../assets/css/product.css">
</head>
<body>
    <header class="p-3 mb-3 border-bottom"> 
        <div class="container"> 
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start"> 
                    <div class="col-md-3 mb-2 mb-md-0"> 
                       <img src="/ModeUnique/images/logo/Black_And_Gold-removebg-preview.png" alt="logo" id="logo">
                    </div> 
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0"> 
                    <li>
                        <a href="/ModeUnique/" class="nav-link px-2 link-secondary">Accueil</a>
                    </li> 
                    <li>
                        <a href="/ModeUnique/views/creations/creationPage.php" class="nav-link px-2 link-body-emphasis">Créations et Produits</a>
                    </li> 
                    <li>
                        <a href="/ModeUnique/views/appointments/contact.php" class="nav-link px-2 link-body-emphasis">Contact</a>
                    </li> 
                    <li>
                        <a href="/ModeUnique/views/appointments/book.php" class="nav-link px-2 link-body-emphasis">Prise de rendez-vous</a>
                    </li> 
                </ul> 
                <div class="col-md-3 text-end">
                    <?php if ($isLoggedIn): ?>
                        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                        <div class="d-flex justify-content-end align-items-center">
                            
                            <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                            <a href="http://127.0.0.1/ModeUnique/views/products/cart.php" class="btn btn-outline-secondary me-2 position-relative">
                                <i class="fas fa-shopping-cart" style="font-size: 1.1rem;"></i> 
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <span id="cart-count"><?= $cartCount ?></span>
                                </span>
                            </a>
                            <?php endif; ?>
                            <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-2" style="font-size: 1.2rem;"></i>
                                <span><?php echo htmlspecialchars($userName); ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end text-small">
                                <li><h6 class="dropdown-header">Bonjour, <?php echo htmlspecialchars($userName); ?>!</h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="/ModeUnique/views/users/settings.php">
                                        <i class="bi bi-gear me-2"></i>Paramètres
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/ModeUnique/views/users/profile.php">
                                        <i class="bi bi-person me-2"></i>Mon Profil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/ModeUnique/views/users/add_review.php">
                                        <i class="bi bi-star-fill me-2"></i>Laisser un avis
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="/ModeUnique/views/logout.php">
                                        <i class="bi bi-box-arrow-right me-2"></i>Se déconnecter
                                    </a>
                                </li>
                            </ul>
                                
                        <div class="dropdown text-end"> 
                </div>  
            </div>  
        </div> 
                        <?php endif; ?>
                        
                    <?php else: ?>
                            <button type="button" class="btn btn-outline-primary me-2" onclick="window.location.href='/ModeUnique/views/login.php'">Se connecter</button> 
                            <button type="button" class="btn btn-primary" onclick="window.location.href='/ModeUnique/views/signup.php'">S'inscrire</button> 
                    <?php endif; ?>
                </div> 
    </header>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>