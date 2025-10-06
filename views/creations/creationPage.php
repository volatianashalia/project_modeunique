<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <base href="http://127.0.0.1/ModeUnique/">
    <title>Mode_Unique Creations</title>
    <link rel="stylesheet" href="../../assets/css/creation.css">
</head>
<body>
        <?php require_once ('../layouts/header.php')?>

    <main class="container"> 
        <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="background-image:url('images/Creaprinci2.jpeg'); background-size:cover; background-repeat:no-repeat;display:flex;justify-content:center;align-items:center;height:400px;"> 
            <div class="col-lg-6 px-0" style="background-color:rgba(255,255,255,0.5); width:75%;"> 
                <h1 class="display-4 fst-italic" style="color:#D4AF37;"><strong>Nos creations</strong></h1> 
                <h3 style="color:black;"><i>"Chaque création est une signature."</i></h3>
            </div> 
        </div> 
    </main>
    <div class="container creationgallery">
        <div class="container intro"  style="margin-bottom:5%;margin-top:5%;">
            <h5>Cette section présente un aperçu visuel de nos réalisations. Chaque modèle est conçu avec exigence, du patronage à la finition, pour illustrer notre maîtrise des volumes, des matières et des détails.</h5>
        </div>
        <h2 class="text-center mb-4"  style="color:#D4AF37;margin-bottom:5%">Nos créations</h2>
        <?php require_once 'list.php';?>
    </div>
    <div class="container product_option" style="text-align:center;">
       <h3 style="color:#D4AF37">Vous n'avez pas de temps pour faire une commande sur mesure?</h3><br>
       <h4 style="color:#D4AF37">Alors que vous voulez un outfit classe?</h4>
       <h5 style="color:#D4AF37">On a une solution pour vous:</h5>
       <p>E-hianjaika vous offre des produits de qualités, hautes coutures et uniques.Vous pouvez faire votre commande en ligne en faisons le choix sur le modele,couleur et taille</p>
        <form action="/ModeUnique/views/products/productPage.php" method="GET">
            <button class="btn btn-primary" style="background-color:#D4AF37;border:none;" >Voir nos produits</button>
        </form>
       
    </div>
        <?php require_once ('../layouts/newsletter.php')?>
        <?php require_once ('../layouts/map.php')?>
    <?php include_once ('../layouts/footer.php')?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>