<?php
include 'firebase-config.php';

if ($_POST && !empty($_SESSION['cart'])) {
    // Calculate total
    $subtotal = 0;
    $itemCount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $itemTotal = $item['price'] * $item['quantity'];
        $subtotal += $itemTotal;
        $itemCount += $item['quantity'];
    }
    
    $deliveryFee = 2.50;
    $tax = $subtotal * 0.085;
    $total = $subtotal + $deliveryFee + $tax;
    
    // Generate unique order ID
    $order_id = 'ORD' . time() . rand(100, 999);
    
    // Prepare order data
    $orderData = [
        'customer' => [
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'address' => trim($_POST['address']),
            'city' => isset($_POST['city']) ? trim($_POST['city']) : '',
            'zipcode' => isset($_POST['zipcode']) ? trim($_POST['zipcode']) : '',
            'special_instructions' => isset($_POST['special_instructions']) ? trim($_POST['special_instructions']) : ''
        ],
        'order_details' => [
            'items' => $_SESSION['cart'],
            'item_count' => $itemCount,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'tax' => $tax,
            'total' => $total,
            'delivery_time' => isset($_POST['delivery_time']) ? $_POST['delivery_time'] : 'asap',
            'payment_method' => isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cash'
        ],
        'status' => 'pending',
        'order_date' => date('Y-m-d H:i:s'),
        'order_id' => $order_id,
        'estimated_delivery' => date('Y-m-d H:i:s', strtotime('+45 minutes'))
    ];
    
    // Validate required fields
    $requiredFields = ['first_name', 'last_name', 'email', 'phone', 'address'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (empty(trim($_POST[$field]))) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        $_SESSION['error'] = 'Please fill in all required fields: ' . implode(', ', $missingFields);
        header('Location: order.php');
        exit();
    }
    
    // Validate email format
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address';
        header('Location: order.php');
        exit();
    }
    
    // Send order to Firebase
    try {
        $result = $firebase->postData('orders', $orderData);
        
        if ($result) {
            // Store order info in session for confirmation page
            $_SESSION['last_order_id'] = $order_id;
            $_SESSION['last_order_items'] = $_SESSION['cart'];
            $_SESSION['last_order_total'] = $total;
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            // Store order data for admin notification (optional)
            $_SESSION['recent_order'] = $orderData;
            
            // Redirect to confirmation
            header('Location: order-confirmation.php?order_id=' . $order_id);
            exit();
        } else {
            throw new Exception('Failed to save order to database');
        }
    } catch (Exception $e) {
        error_log("Order processing error: " . $e->getMessage());
        $_SESSION['error'] = 'Sorry, there was an error processing your order. Please try again.';
        header('Location: order.php');
        exit();
    }
} else {
    // No cart items or invalid request
    if (empty($_SESSION['cart'])) {
        $_SESSION['error'] = 'Your cart is empty. Please add items before checking out.';
    } else {
        $_SESSION['error'] = 'Invalid request. Please try again.';
    }
    header('Location: cart.php');
    exit();
}
?>