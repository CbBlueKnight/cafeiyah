<?php
include 'firebase-config.php';

if (isset($_GET['index']) && isset($_GET['quantity'])) {
    $index = $_GET['index'];
    $quantity = intval($_GET['quantity']);
    
    if (isset($_SESSION['cart'][$index]) && $quantity > 0) {
        $_SESSION['cart'][$index]['quantity'] = $quantity;
    }
}

header('Location: cart.php');
exit();
?>