<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ .'/../../config/auth.php';
require_once __DIR__ .'/../../config/DB.php';

if (!is_logged_in()) {
    header('Location: /ModeUnique/views/login.php');
    exit();
}

require_once('../layouts/header.php');

$user_id = $_SESSION['user_id'];
$appointments = [];

try {
    $stmt = $pdo->prepare(
        "SELECT * FROM appointments 
         WHERE user_id = ? 
         ORDER BY date DESC, time DESC"
    );
    $stmt->execute([$user_id]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erreur my_appointment: " . $e->getMessage());
    $error_message = "Une erreur est survenue lors du chargement de vos rendez-vous.";
}

function formatDateFr($date) {
    $timestamp = strtotime($date);
    $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    $mois = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    
    $jour_semaine = $jours[date('w', $timestamp)];
    $jour = date('d', $timestamp);
    $mois_nom = $mois[date('n', $timestamp)];
    $annee = date('Y', $timestamp);
    
    return "$jour_semaine $jour $mois_nom $annee";
}

$services = [
    'consultation' => 'Consultation Générale',
    'prise_mesures' => 'Prise de Mesures',
    'essayage' => 'Essayage',
    'retouches' => 'Retouches',
    'creation_sur_mesure' => 'Création sur Mesure'
];

$statuts = [
    'confirmed' => 'Confirmé',
    'cancelled' => 'Annulé',
    'completed' => 'Terminé'
];
$statut_colors = [
    'confirmed' => '#28a745',
    'cancelled' => '#dc3545',
    'completed' => '#6c757d'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Mes Rendez-vous</title>
    <style>
        .page-header {
            background: linear-gradient(135deg, #D4AF37 0%, #b8941f 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        
        .appointment-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
            border-left: 5px solid #D4AF37;
            transition: transform 0.2s;
        }
        
        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .appointment-date {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
        }
        
        .appointment-time {
            font-size: 1.1em;
            color: #D4AF37;
            font-weight: 600;
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            color: white;
            font-weight: 500;
            font-size: 0.9em;
        }
        
        .appointment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 0.85em;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 1em;
            color: #333;
            font-weight: 500;
        }
        
        .notes-section {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        
        .no-appointments {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .no-appointments i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .btn-book {
            background-color: #D4AF37;
            border: none;
            color: white;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-book:hover {
            background-color: #b8941f;
            color: white;
        }
        
        .action-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        
        .btn-cancel {
            padding: 8px 20px;
            border: 1px solid #dc3545;
            color: #dc3545;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-cancel:hover {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <h1>Mes rendez-vous</h1>
            <p class="mb-0">Gérez vos rendez-vous avec notre équipe</p>
        </div>
    </div>

    <div class="container mb-5">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (empty($appointments)): ?>
            <div class="no-appointments">
                <div style="font-size: 80px; color: #ddd;">📅</div>
                <h3>Vous n'avez pas encore de rendez-vous.</h3>
                <p class="text-muted">Prenez rendez-vous avec notre équipe d'experts en couture.</p>
                <a href="book.php" class="btn-book mt-3">Prendre un rendez-vous</a>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Vos rendez-vous (<?php echo count($appointments); ?>)</h2>
                <a href="book.php" class="btn-book">Nouveau rendez-vous</a>
            </div>

            <?php foreach ($appointments as $appointment): ?>
                <div class="appointment-card">
                    <div class="appointment-header">
                        <div>
                            <div class="appointment-date">
                                <?php echo formatDateFr($appointment['date']); ?>
                            </div>
                            <div class="appointment-time">
                                🕐 <?php echo substr($appointment['time'], 0, 5); ?>
                            </div>
                        </div>
                        <div>
                            <?php 
                            $status = $appointment['status'];
                            $color = $statut_colors[$status] ?? '#6c757d';
                            ?>
                            <span class="status-badge" style="background-color: <?php echo $color; ?>;">
                                <?php echo $statuts[$status] ?? $status; ?>
                            </span>
                        </div>
                    </div>

                    <div class="appointment-details">
                        <div class="detail-item">
                            <span class="detail-label">Service</span>
                            <span class="detail-value">
                                <?php echo $services[$appointment['service']] ?? $appointment['service']; ?>
                            </span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Nom</span>
                            <span class="detail-value">
                                <?php echo htmlspecialchars($appointment['full_name']); ?>
                            </span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value">
                                <?php echo htmlspecialchars($appointment['email']); ?>
                            </span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Téléphone</span>
                            <span class="detail-value">
                                <?php echo htmlspecialchars($appointment['phone']); ?>
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($appointment['notes'])): ?>
                        <div class="notes-section">
                            <span class="detail-label">Notes</span>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($appointment['notes'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($appointment['status'] === 'confirmed'): ?>
                        <div class="action-buttons">
                            <button class="btn-cancel" onclick="cancelAppointment(<?php echo $appointment['id']; ?>)">
                                Annuler ce rendez-vous
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include_once('../layouts/footer.php'); ?>

    <script>
        function cancelAppointment(id) {
            if (confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?')) {
                fetch('cancel_appointment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur de communication est survenue. Veuillez réessayer.');
                });
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

