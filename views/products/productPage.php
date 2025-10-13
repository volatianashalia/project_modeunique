<?php
require_once __DIR__ . '/../../config/DB.php';
$all_categories = [];
try {
    $categoriesStmt = $pdo->query("SELECT id, Name FROM categories ORDER BY Name ASC");
    $all_categories = $categoriesStmt->fetchAll();
} catch (Exception $e) {
    error_log("Erreur lors de la récupération des catégories pour le dropdown: " . $e->getMessage());
}

$products = [];
$category_name = "Nos produits"; 

try {
    if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
        $category_id = intval($_GET['category_id']);
        $catStmt = $pdo->prepare("SELECT Name FROM categories WHERE id = ?");
        $catStmt->execute([$category_id]);
        $category = $catStmt->fetch();
        if ($category) {
            $category_name = "Produits de la catégorie : " . htmlspecialchars($category['Name']);
        }
        $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY created_at DESC");
        $stmt->execute([$category_id]);
    } else {
        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    }
    $products = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Erreur lors de la récupération des produits: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Produit_ModeUnique</title>
    <link rel="stylesheet" href="../../assets/css/product.css">
</head>
<body>
        <?php require_once __DIR__ '../layouts/header.php'; ?>
        <main class="container"> 
            <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="background-image:url('../../images/Creaprinci2.jpeg'); background-size:cover; background-repeat:no-repeat;display:flex;justify-content:center;align-items:center;height:400px;"> 
                <div class="col-lg-6 px-0" style="background-color:rgba(255,255,255,0.5); width:75%;"> 
                    <h1 class="display-4 fst-italic" style="color:#D4AF37;"><strong><?php echo $category_name; ?></strong></h1> 
                    <h3 style="color:black;"><i>"Chaque création est une signature."</i></h3> 
                     <p class="lead my-3" style="color:black;text-align:center;">Pensées comme des pièces uniques, nos créations allient exigence technique, élégance intemporelle et audace maîtrisée. Chaque robe, chaque ensemble, chaque détail incarne notre vision : celle d’une couture qui raconte une histoire, sublime les silhouettes et traverse les saisons avec grâce. Du choix des matières à la finition main, nous cultivons une esthétique où le luxe se fait discret, et l’émotion palpable.</p>  
                 </div> 
            </div> 
        </main>
        <div class="container creationgallery">
            <div class="container categorychoice">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Catégories
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="productPage.php">Toutes les catégories</a></li>
                        <?php if (!empty($all_categories)): ?>
                            <li><hr class="dropdown-divider"></li>
                            <?php foreach ($all_categories as $cat): ?>
                                <li><a class="dropdown-item" href="productPage.php?category_id=<?= $cat['id'] ?>">
                                    <?= htmlspecialchars($cat['Name']) ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="container intro">
                <h5>Cette section présente un aperçu visuel de nos produits. Chaque modèle est conçu avec exigence, du patronage à la finition, pour illustrer notre maîtrise des volumes, des matières et des détails.Pour voir plus de détails pour chaque produit , veuillez cliquez sur les boutons "Voir les détails"</h5>
            </div>
        </div>
        <?php 
        require_once 'list.php';
        ?>
    <?php require_once __DIR__'../layouts/newsletter.php'; ?>
    <?php require_once __DIR__'../layouts/map.php'; ?>
    <?php require_once __DIR__ '../layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
