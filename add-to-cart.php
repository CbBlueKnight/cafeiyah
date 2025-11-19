<?php
include 'firebase-config.php';

if ($_POST) {
    $item_id = $_POST['item_id'];
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    
    // Check if item already exists in cart
    $item_exists = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $item_id) {
            $item['quantity'] += 1;
            $item_exists = true;
            break;
        }
    }
    
    // If item doesn't exist, add it to cart
    if (!$item_exists) {
        $_SESSION['cart'][] = [
            'id' => $item_id,
            'name' => $name,
            'price' => $price,
            'quantity' => 1
        ];
    }
    
    echo "success";
}
?>