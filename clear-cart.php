<?php
include 'firebase-config.php';

// Clear the entire cart
$_SESSION['cart'] = [];

// Set success message
$_SESSION['success'] = 'Cart cleared successfully';

// Redirect back to cart
header('Location: cart.php');
exit();
?>