<?php
require_once('../../../config/DB.php');
header('Content-Type: application/json');

if (!empty($_POST['id'])) {
    $id = (int) $_POST['id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}

// $id = intval($_GET['id']);
// $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
// $stmt->execute([$id]);
// $client = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id= (int)$_POST['category_id'];
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
      $size = trim($_POST['size']);
    $description = trim($_POST['description']);
    $image = trim($_POST['images']);
    $size = trim($_POST['size']);

    $stmt = $pdo->prepare("UPDATE products SET name=?, image=?, crated_at=?,description=?, price=?, stock=?, size=?,category_id=?, WHERE id=?");
    $stmt->execute([$name, $image, $description,  $price, $location, $stock ,$size,$category_id]);
    header('Location: admin.php#products');
    exit;
}
?>
<form method="post">
    <input type="text" name="first_name" value="<?= htmlspecialchars($product['name']) ?>" required>
    <input type="text" name="last_name" value="<?= htmlspecialchars($product['image']) ?>" required>
    <input type="email" name="email" value="<?= htmlspecialchars($product['desccription']) ?>" required>
    <input type="tel" name="phone" value="<?= htmlspecialchars($product['price']) ?>" required>
    <input type="text" name="location" value="<?= htmlspecialchars($product['stock']) ?>" required>
    <input type="text" name="location" value="<?= htmlspecialchars($product['size ']) ?>" required>
    <label class="form-label">Catégorie</label>
        <select class="form-select" name="category_id" required>
            <?php
                // récupération des catégories
                $stmt = $pdo->query("SELECT id, name FROM categories");
                while ($row = $stmt->fetch()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
            ?>
        </select>
    <button type="submit">Enregistrer</button>
</form>