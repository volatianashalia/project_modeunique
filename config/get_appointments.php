<?php
require_once __DIR__ 'DB.php';
header('Content-Type: application/json');


try {
    $stmt = $pdo->query("
        SELECT 
            appointments.id,
            appointments.full_name,
            appointments.email,
            appointments.phone,
            appointments.service,
            appointments.date,
            appointments.time,
            appointments.notes,
            appointments.status,
            users.first_name,
            users.last_name
        FROM appointments
        LEFT JOIN users ON appointments.user_id = users.id
        ORDER BY appointments.date DESC, appointments.time DESC
    ");

    $appointments = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $appointments[] = [
            'id'        => $row['id'],
            'clientName'=> $row['full_name'] ?: ($row['first_name'] . ' ' . $row['last_name']),
            'email'     => $row['email'],
            'phone'     => $row['phone'],
            'service'   => $row['service'],
            'date'      => $row['date'],
            'time'      => $row['time'],
            'notes'     => $row['notes'],
            'status'    => $row['status'] ?? 'Scheduled'
        ];
    }

    echo json_encode($appointments);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
