<?php
require_once __DIR__ .'/../../config/auth.php';

if (!is_logged_in()) {
    header('Location: /ModeUnique/views/login.php?redirect_url=/ModeUnique/views/appointments/book.php&notice=login_required_for_booking');
}
require_once ('../layouts/header.php');
$user_full_name = htmlspecialchars($_SESSION['first_name'] ?? '') . ' ' . htmlspecialchars($_SESSION['last_name'] ?? '');
$user_email = htmlspecialchars($_SESSION['email'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Contact</title>
    <link rel="stylesheet" href="../../assets/css/book.css">
</head>
<body>
<div class="container">
        <header class="header">
            <h1>Prendre Rendez-vous</h1>
            <p>Planifiez une consultation avec notre équipe d'experts en couture</p>
        </header>

        <div class="booking-container">
            <div class="calendar-section">
                <h2>Sélectionner la Date</h2>
                <div class="calendar-header">
                    <button id="prevMonth" class="nav-btn">&lt;</button>
                    <h3 id="currentMonth"></h3>
                    <button id="nextMonth" class="nav-btn">&gt;</button>
                </div>
                <div class="calendar-grid" id="calendarGrid"></div>
            </div>

            <div class="time-section">
                <h2>Créneaux Disponibles</h2>
                <div class="time-slots" id="timeSlots">
                    <p class="select-date-first">Veuillez d'abord sélectionner une date</p>
                </div>
            </div>

            <div class="form-section">
                <h2>Vos Informations</h2>
                <form id="appointmentForm" class="appointment-form" action="process_appointment.php" method="POST">
                    <div class="form-group">
                        <label for="fullName">Nom Complet *</label>
                        <input type="text" id="fullName" name="fullName" required value="<?php echo trim($user_full_name); ?>">
                        <span class="error-message" id="fullNameError"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">Adresse Email *</label>
                        <input type="email" id="email" name="email" required value="<?php echo $user_email; ?>">
                        <span class="error-message" id="emailError"></span>
                    </div>

                    <div class="form-group">
                        <label for="phone">Numéro de Téléphone *</label>
                        <input type="tel" id="phone" name="phone" required placeholder="Votre numéro ici">
                        <span class="error-message" id="phoneError"></span>
                    </div>

                    <div class="form-group">
                        <label for="service">Type de Service *</label>
                        <select id="service" name="service" required>
                            <option value="">Sélectionnez un service</option>
                            <option value="consultation">Consultation Générale</option>
                            <option value="prise_mesures">Prise de Mesures</option>
                            <option value="essayage">Essayage</option>
                            <option value="retouches">Retouches</option>
                            <option value="creation_sur_mesure">Création sur Mesure</option>
                        </select>
                        <span class="error-message" id="serviceError"></span>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes Supplémentaires</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Exigences particulières ou notes..."></textarea>
                        <span class="error-message" id="notesError"></span>
                    </div>

                    <div class="appointment-summary" id="appointmentSummary" style="display: none;">
                        <h3 style=" color: #D4AF37;">Résumé du Rendez-vous</h3>
                        <div class="summary-details">
                            <p><strong>Date:</strong> <span id="summaryDate"></span></p>
                            <p><strong>Heure:</strong> <span id="summaryTime"></span></p>
                            <p><strong>Service:</strong> <span id="summaryService"></span></p>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="submit-btn" disabled>
                        Réserver le Rendez-vous
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="success-modal" id="successModal">
        <div class="modal-content">
            <div class="success-icon">✓</div>
            <h2>Rendez-vous Réservé !</h2>
            <p>Votre rendez-vous a été programmé avec succès. Vous recevrez un email de confirmation sous peu.</p>
            <button id="closeModal" class="close-btn">Fermer</button>
        </div>
    </div>
    <?php include_once ('../layouts/footer.php')?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
     <script src="../../assets/js/book.js"></script>  
</body>
</html>