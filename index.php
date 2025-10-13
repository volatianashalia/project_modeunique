<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Mode unique</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert" style="position: fixed; top: 10px; right: 10px; z-index: 1050;">
            Vous avez été déconnecté avec succès.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php require_once __DIR__ 'views/layouts/header.php'?>
    <main class="container"> 
        <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="background-image:url('images/firstimage.jpg'); background-size:cover;"> 
            <div class="col-lg-6 px-0" style="background-color:rgba(0,0,0,0.5);"> 
                <h1 class="display-4 fst-italic" style="color:#D4AF37;"><strong>E-hianjaika</strong></h1> 
                <h3 style="color:white;"><i>"L’élégance sur mesure, façonnée pour vous."</i></h3>
                <p class="lead my-3" style="color:white; text-align:center;">Maison de Couture, symbole d’excellence et de savoir-faire, conçoit des créations uniques alliant tradition et innovation. Chaque pièce est réalisée avec une précision artisanale et une exigence absolue de qualité, afin d’offrir à nos clients une expérience sur mesure, empreinte d’élégance et de distinction.</p> 
                <div class="bouton" style="justify-content: center;">
                    <button class="btn btn-primary" id="voircrea">
                        Voir nos créations
                    </button>
                </div>
            </div> 
        </div> 
    </main>
    <div class="container" id="savoirfaire">
       <strong><h2> L’art du sur-mesure</h2></strong> 
       <div class="row" style="width:100%;">
            <div class="col-md-4" style="width:50%;" style="display:flex;align-items:center;">
                <p style="text-align:center;">
                Chaque création est une rencontre entre votre personnalité et notre exigence. Des tissus d’exception, une confection artisanale et un design pensé pour sublimer chaque silhouette.
                </p>
                <div class="row" width="50%">
                    <h3 style="color:#D4AF37;">Ce qui rend chaque création unique:</h3>
                    <div class="col">
                        <button>
                            1
                            <br>Confection artisanale
                        </button>
                    </div>
                    <div class="col">
                    <button>
                            2
                            <br>Tissus d’exception
                        </button>
                    </div>
                    <div class="col">
                    <button>
                           3
                           <br>Design personnalisé
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4" id="image1" style="width:50%;">
                <img src="images/header 3.png" alt="photo1">
            </div>
       </div>
    </div>
    <div class="container creation">
        <h2 style="margin-bottom:5%;"> Nos créations récentes:</h2>
        <div class="row">
            <?php 
           require_once __DIR__ 'views/creations/list.php';
            ?>
        </div>
    </div>
    <?php require_once __DIR__'views/products/category.php';?>
    <div class="container service" id="services">
        <h2 style="color: #D4AF37; text-align: center; margin-top: 5%; margin-bottom: 3%;">Les services que nous offrons:</h2>
        <div class="row">
            <div class="col-md-4">
                <h4>1 <br> Création <br>sur mesure</h4>
            </div>
            <div class="col-md-4">
                <h4>2<br> Haute couture prêt-à-porter</h4>
            </div>
            <div class="col-md-4">
                <h4>3 <br> Consultation</h4>
            </div>
        </div>
    </div>
    <?php require_once __DIR__ 'views/layouts/newsletter.php';?>
    <?php require_once __DIR__ 'views/layouts/reviews.php';?>

    <?php require_once __DIR__ 'views/layouts/map.php';?>
    <?php require_once __DIR__ 'views/layouts/footer.php';?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
