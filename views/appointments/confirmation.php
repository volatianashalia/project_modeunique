<?php
require_once __DIR__ .'/../../config/auth.php';
require_once __DIR__ .'/../../config/DB.php';

if (!is_logged_in()) {
    header('Location: /ModeUnique/views/login.php');
    exit();
}

require_once('../layouts/header.php');

$user_id = $_SESSION['user_id'];
$appointment = null;

try {
    $stmt = $pdo->prepare(
        "SELECT * FROM appointments 
         WHERE user_id = ? 
         ORDER BY created_at DESC 
         LIMIT 1"
    );
    $stmt->execute([$user_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Erreur confirmation: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Confirmation de Rendez-vous</title>
    <style>
        .confirmation-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 30px;
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            text-align: center;
            margin-bottom: 20px;
        }
        .appointment-details {
            background-color: #f8f9fa;
            border-left: 4px solid #D4AF37;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        .detail-value {
            color: #212529;
        }
        .btn-custom {
            background-color: #D4AF37;
            border: none;
            color: white;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-custom:hover {
            background-color: #b8941f;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container confirmation-container">
        <div class="text-center">
            <div class="success-icon">✓</div>
            <h1 class="mb-4">Rendez-vous Confirmé !</h1>
            <p class="lead">Votre rendez-vous a été enregistré avec succès.</p>
        </div>

        <?php if ($appointment): ?>
        <div class="appointment-details">
            <h3 class="mb-3" style="color: #D4AF37;">Détails du Rendez-vous</h3>
            
            <div class="detail-row">
                <span class="detail-label">Nom :</span>
                <span class="detail-value"><?php echo htmlspecialchars($appointment['full_name']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Email :</span>
                <span class="detail-value"><?php echo htmlspecialchars($appointment['email']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Téléphone :</span>
                <span class="detail-value"><?php echo htmlspecialchars($appointment['phone']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Date :</span>
                <span class="detail-value">
                    <?php 
                    $date = new DateTime($appointment['date']);
                    echo $date->format('l d F Y');
                    ?>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Heure :</span>
                <span class="detail-value"><?php echo substr($appointment['time'], 0, 5); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Service :</span>
                <span class="detail-value">
                    <?php 
                    $services = [
                        'consultation' => 'Consultation Générale',
                        'prise_mesures' => 'Prise de Mesures',
                        'essayage' => 'Essayage',
                        'retouches' => 'Retouches',
                        'creation_sur_mesure' => 'Création sur Mesure'
                    ];
                    echo $services[$appointment['service']] ?? $appointment['service'];
                    ?>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Statut :</span>
                <span class="detail-value" style="color: #28a745; font-weight: bold;">
                    ✓ Confirmé
                </span>
            </div>
        </div>

        <div class="alert alert-info mt-4">
            <strong>📧 Email de confirmation</strong><br>
            Un email de confirmation a été envoyé à votre adresse. 
            Veuillez vérifier votre boîte de réception.
        </div>
        <?php else: ?>
            <div class="alert alert-warning">
                Aucun rendez-vous trouvé.
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="/ModeUnique/index.php" class="btn-custom me-2">Retour à l'accueil</a>
            <a href="my_appointment.php" class="btn-custom">Voir mes rendez-vous</a>
        </div>
    </div>

    <?php include_once('../layouts/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
