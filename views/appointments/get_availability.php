<?php
header('Content-Type: application/json');

require_once __DIR__ .'/../../config/DB.php';
$all_slots = [
    '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
    '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00'
];

$date = $_GET['date'] ?? null;
if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode([]); 
    exit;
}

$booked_slots = [];

try {
    $stmt = $pdo->prepare("SELECT time FROM appointments WHERE date = ?");
    $stmt->execute([$date]);
    $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($results as $booked_time) {
        $booked_slots[] = substr($booked_time, 0, 5);
    }

} catch (Exception $e) {
    error_log("Erreur get_availability: " . $e->getMessage());
    echo json_encode([]);
    exit;
}

$available_slots = array_diff($all_slots, $booked_slots);
echo json_encode(array_values($available_slots)); 
