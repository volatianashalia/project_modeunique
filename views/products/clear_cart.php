<?php
session_start();

// Vider le panier
$_SESSION['cart'] = [];

// Rediriger vers la page du panier avec un message
header('Location: cart.php?success=cleared');
exit();
?>