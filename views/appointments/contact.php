<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <title>Contact</title>
    <link rel="stylesheet" href="../../assets/css/contact.css">
</head>
<body>
        <?php require_once ('../layouts/header.php')?>
    <div class="container-fluid contact-hero" style="background-image: url('/ModeUnique/images/pexels-ron-lach-9849657.jpg');background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat;
    background-attachment: fixed;
    min-height: 500px;
    display: flex;align-items: center;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold" style="color: #D4AF37;">Contactez-nous</h1>
                    <p class="lead" style="background-color:rgba(255,255,255,0.5); color:black; padding:2%;">Nous sommes là pour répondre à toutes vos questions et vous accompagner dans vos projets de couture sur mesure.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container contact-info">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Adresse</h4>
                    <p>VK3PU Morarano<br>Antananarivo, Madagascar</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h4>Téléphone</h4>
                    <p>+261 34 63 460 50</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4>Email</h4>
                    <p>ehianjaika@gmail.com</p>
                </div>
            </div>
        </div>
    </div>

    <section class="contact-form-section" style="padding: 60px 0; background-color: #f8f9fa;">
    <div class="container">
        <h2 class="text-center mb-5" style="color: #D4AF37; font-weight: bold;">Envoyez-nous un message</h2>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0" style="border-radius: 15px;">
                    <div class="card-body p-5">
                        <form id="contactForm" action="process_contact.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                                    <div class="invalid-feedback">Veuillez entrer votre prénom.</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" required>
                                    <div class="invalid-feedback">Veuillez entrer votre nom.</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback">Veuillez entrer une adresse email valide.</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Téléphone (optionnel)</label>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Sujet de votre message <span class="text-danger">*</span></label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Choisissez un sujet</option>
                                    <option value="Demande de devis">Demande de devis</option>
                                    <option value="Prise de rendez-vous">Prise de rendez-vous</option>
                                    <option value="Question sur un produit">Question sur un produit</option>
                                    <option value="Retouche">Retouche</option>
                                    <option value="Réclamation">Réclamation</option>
                                    <option value="Autre">Autre</option>
                                </select>
                                <div class="invalid-feedback">Veuillez choisir un sujet.</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="form-label">Votre message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
                                <div class="invalid-feedback">Veuillez entrer votre message.</div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-lg" style="background-color: #D4AF37; color: white; border: none; border-radius: 8px;">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer le message
                                </button>
                            </div>
                        </form>
                        <div id="formMessage" class="mt-3" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <div class="container-fluid map-section">
        <div class="container">
            <h2 class="text-center mb-4" style="color: #D4AF37;">Nous trouver</h2>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="map-container">
                        <img src="../../images/Map.png" alt="Localisation E-hianjaika" class="img-fluid rounded" style="width:100%; height:auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container hours-section">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="hours-card">
                    <h3 class="text-center mb-4" style="color: #D4AF37;">
                        <i class="fas fa-clock me-2"></i>Horaires d'ouverture
                    </h3>
                    <div class="hours-list">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Lundi - Vendredi</span>
                            <span>9h00 - 18h00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Samedi</span>
                            <span>9h00 - 16h00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Dimanche</span>
                            <span>Sur rendez-vous</span>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <a href="book.php" class="btn btn-outline-primary" style="background-color:white;border-color: #D4AF37;color:#D4AF37;">
                            <i class="fas fa-calendar-alt me-2"></i>Prendre rendez-vous
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once ('../layouts/footer.php')?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="../../assets/js/contact.js"></script>
</body>
</html>