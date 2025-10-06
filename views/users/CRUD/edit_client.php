<?php
require_once('../../../config/DB.php');
$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $location = trim($_POST['location']);
    $preferences = trim($_POST['preferences']);
    $status = trim($_POST['status']);

    $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, email=?, phone=?, location=?, preferences=?, status=? WHERE id=?");
    $stmt->execute([$first_name, $last_name, $email, $phone, $location, $preferences, $status, $id]);
    header('Location: admin.php#clients');
    exit;
}
?>
<form method="post">
    <input type="text" name="first_name" value="<?= htmlspecialchars($client['first_name']) ?>" required>
    <input type="text" name="last_name" value="<?= htmlspecialchars($client['last_name']) ?>" required>
    <input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required>
    <input type="tel" name="phone" value="<?= htmlspecialchars($client['phone']) ?>" required>
    <input type="text" name="location" value="<?= htmlspecialchars($client['location']) ?>" required>
    <textarea name="preferences"><?= htmlspecialchars($client['preferences']) ?></textarea>
    <select name="status">
        <option value="Active" <?= $client['status']=='Active'?'selected':'' ?>>Active</option>
        <option value="VIP" <?= $client['status']=='VIP'?'selected':'' ?>>VIP</option>
        <option value="Inactive" <?= $client['status']=='Inactive'?'selected':'' ?>>Inactive</option>
    </select>
    <button type="submit">Enregistrer</button>
</form>