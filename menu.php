<?php
session_start();

// Initialize menu items if not set
if (!isset($_SESSION['menu_items'])) {
    $_SESSION['menu_items'] = [
        [
            'id' => '1',
            'name' => 'Classic Espresso',
            'price' => 120,
            'category' => 'Espresso',
            'description' => 'Strong and rich espresso shot',
            'image' => '☕'
        ],
        [
            'id' => '2',
            'name' => 'Caramel Macchiato',
            'price' => 150,
            'category' => 'Espresso',
            'description' => 'Espresso with caramel and steamed milk',
            'image' => '☕'
        ],
        [
            'id' => '3',
            'name' => 'Americano',
            'price' => 110,
            'category' => 'Espresso',
            'description' => 'Espresso with hot water',
            'image' => '☕'
        ],
        [
            'id' => '4',
            'name' => 'Chocolate Frappe',
            'price' => 140,
            'category' => 'Frappe',
            'description' => 'Iced blended chocolate drink',
            'image' => '🥤'
        ],
        [
            'id' => '5',
            'name' => 'Strawberry Frappe',
            'price' => 145,
            'category' => 'Frappe',
            'description' => 'Refreshing strawberry blended drink',
            'image' => '🥤'
        ],
        [
            'id' => '6',
            'name' => 'Cookies & Cream Frappe',
            'price' => 155,
            'category' => 'Frappe',
            'description' => 'Blended cookies and cream delight',
            'image' => '🥤'
        ],
        [
            'id' => '7',
            'name' => 'Classic Milk Tea',
            'price' => 110,
            'category' => 'Milk Tea',
            'description' => 'Traditional milk tea with pearls',
            'image' => '🧋'
        ],
        [
            'id' => '8',
            'name' => 'Wintermelon Milk Tea',
            'price' => 120,
            'category' => 'Milk Tea',
            'description' => 'Sweet wintermelon flavor with milk',
            'image' => '🧋'
        ],
        [
            'id' => '9',
            'name' => 'Taro Milk Tea',
            'price' => 125,
            'category' => 'Milk Tea',
            'description' => 'Creamy taro flavored milk tea',
            'image' => '🧋'
        ],
        [
            'id' => '10',
            'name' => 'Strawberry Soda',
            'price' => 95,
            'category' => 'Fruit Soda',
            'description' => 'Sparkling strawberry soda',
            'image' => '🍹'
        ],
        [
            'id' => '11',
            'name' => 'Blue Lemonade',
            'price' => 90,
            'category' => 'Fruit Soda',
            'description' => 'Refreshing blue lemonade soda',
            'image' => '🍹'
        ],
        [
            'id' => '12',
            'name' => 'Green Apple Soda',
            'price' => 95,
            'category' => 'Fruit Soda',
            'description' => 'Tangy green apple sparkling drink',
            'image' => '🍹'
        ],
        [
            'id' => '13',
            'name' => 'Chicken Rice Bowl',
            'price' => 180,
            'category' => 'Rice Meals',
            'description' => 'Grilled chicken with steamed rice',
            'image' => '🍛'
        ],
        [
            'id' => '14',
            'name' => 'Beef Caldereta',
            'price' => 220,
            'category' => 'Rice Meals',
            'description' => 'Traditional beef stew with rice',
            'image' => '🍛'
        ],
        [
            'id' => '15',
            'name' => 'Pork Adobo',
            'price' => 190,
            'category' => 'Rice Meals',
            'description' => 'Classic Filipino adobo with rice',
            'image' => '🍛'
        ]
    ];
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart
if ($_POST['action'] === 'add_to_cart') {
    $item_id = $_POST['item_id'];
    $quantity = intval($_POST['quantity']);
    
    // Find the item in menu
    foreach ($_SESSION['menu_items'] as $item) {
        if ($item['id'] == $item_id) {
            // Check if item already in cart
            $found = false;
            foreach ($_SESSION['cart'] as &$cart_item) {
                if ($cart_item['id'] == $item_id) {
                    $cart_item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            
            // If not found, add new item to cart
            if (!$found) {
                $cart_item = $item;
                $cart_item['quantity'] = $quantity;
                $_SESSION['cart'][] = $cart_item;
            }
            
            $success = "{$quantity} x {$item['name']} added to cart!";
            break;
        }
    }
}

// Handle remove from cart
if ($_GET['action'] === 'remove_from_cart') {
    $item_id = $_GET['item_id'];
    
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $item_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            $success = "Item removed from cart!";
            break;
        }
    }
}

// Handle update quantity
if ($_POST['action'] === 'update_quantity') {
    $item_id = $_POST['item_id'];
    $quantity = intval($_POST['quantity']);
    
    if ($quantity <= 0) {
        // Remove item if quantity is 0 or less
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $item_id) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break;
            }
        }
    } else {
        // Update quantity
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $item_id) {
                $item['quantity'] = $quantity;
                break;
            }
        }
    }
}

// Handle checkout
if ($_POST['action'] === 'checkout') {
    $name = $_POST['customer_name'];
    $phone = $_POST['customer_phone'];
    $address = $_POST['customer_address'];
    $payment_method = $_POST['payment_method'];
    $special_instructions = $_POST['special_instructions'];
    
    if (empty($_SESSION['cart'])) {
        $error = "Your cart is empty! Please add some items before checkout.";
    } else {
        // Process order
        $order_total = 0;
        $order_items = [];
        
        foreach ($_SESSION['cart'] as $item) {
            $order_total += $item['price'] * $item['quantity'];
            $order_items[] = $item;
        }
        
        // Add tax (8%)
        $tax = $order_total * 0.08;
        $final_total = $order_total + $tax;
        
        $_SESSION['last_order'] = [
            'items' => $order_items,
            'subtotal' => $order_total,
            'tax' => $tax,
            'total' => $final_total,
            'customer_name' => $name,
            'customer_phone' => $phone,
            'customer_address' => $address,
            'payment_method' => $payment_method,
            'special_instructions' => $special_instructions,
            'order_time' => date('Y-m-d H:i:s'),
            'order_number' => 'ORD-' . date('Ymd') . '-' . rand(1000, 9999)
        ];
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        $success = "Order placed successfully! Your order number is: " . $_SESSION['last_order']['order_number'];
    }
}

// Calculate cart totals
$cart_total = 0;
$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $cart_count += $item['quantity'];
}

$tax = $cart_total * 0.08;
$final_total = $cart_total + $tax;

// Cafe theme settings
$cafe_theme = [
    'primary_color' => '#8B4513',
    'secondary_color' => '#D2691E',
    'cafe_name' => 'Cafe Iyah'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $cafe_theme['cafe_name']; ?> - Menu & Order</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        :root {
            --primary-color: <?php echo $cafe_theme['primary_color']; ?>;
            --secondary-color: <?php echo $cafe_theme['secondary_color']; ?>;
            --accent-color: #f4a261;
            --light-color: #f8f9fa;
            --dark-color: #2d1b0e;
            --text-color: #5a3921;
            --success-color: #27ae60;
            --warning-color: #e67e22;
            --error-color: #e74c3c;
        }

        body {
            background: linear-gradient(135deg, #fef5e7 0%, #f8d8b0 100%);
            color: var(--text-color);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
        }

        /* Home Button */
        .home-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
            z-index: 100;
            text-decoration: none;
        }

        .home-button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.4);
        }

        .home-button i {
            font-size: 20px;
        }

        /* Header Styles */
        .header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 3px solid var(--primary-color);
            margin-top: 10px;
        }

        .logo {
            margin-bottom: 20px;
        }

        .logo i {
            font-size: 48px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

        .logo h1 {
            font-size: 36px;
            color: var(--dark-color);
            font-weight: 700;
            font-family: 'Georgia', serif;
            margin-bottom: 10px;
        }

        .logo p {
            color: var(--text-color);
            font-size: 16px;
            opacity: 0.8;
        }

        /* Navigation */
        .nav-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .nav-tab {
            padding: 12px 24px;
            background: white;
            border: 2px solid var(--primary-color);
            border-radius: 25px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
        }

        .nav-tab:hover, .nav-tab.active {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
        }

        /* Main Content Layout */
        .main-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .home-button {
                top: 10px;
                left: 10px;
                width: 45px;
                height: 45px;
            }
            
            .home-button i {
                font-size: 18px;
            }
        }

        /* Menu Section */
        .menu-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 24px;
            color: var(--dark-color);
            margin-bottom: 25px;
            font-family: 'Georgia', serif;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .category-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .category-btn {
            padding: 8px 16px;
            background: #fef5e7;
            border: 2px solid transparent;
            border-radius: 20px;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .category-btn:hover, .category-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .menu-item-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: 2px solid #f8f8f8;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            border-color: var(--primary-color);
        }

        .menu-item-emoji {
            font-size: 48px;
            text-align: center;
            margin-bottom: 15px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-item-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .menu-item-category {
            display: inline-block;
            padding: 4px 12px;
            background: #fef5e7;
            color: var(--text-color);
            border-radius: 15px;
            font-size: 12px;
            margin-bottom: 12px;
        }

        .menu-item-desc {
            color: var(--text-color);
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.4;
            min-height: 40px;
        }

        .menu-item-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .menu-item-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .add-to-cart-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 2px solid #e1e5ee;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }

        .add-cart-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .add-cart-btn:hover {
            background: #e76f51;
            transform: scale(1.05);
        }

        /* Cart Section */
        .cart-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .cart-title {
            font-size: 20px;
            color: var(--dark-color);
            font-family: 'Georgia', serif;
        }

        .cart-count {
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .cart-items {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .cart-item-price {
            color: var(--primary-color);
            font-weight: 600;
        }

        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border: 2px solid var(--primary-color);
            background: white;
            color: var(--primary-color);
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.3s;
        }

        .qty-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .remove-item {
            color: var(--error-color);
            cursor: pointer;
            margin-left: 10px;
            padding: 5px;
        }

        .remove-item:hover {
            transform: scale(1.1);
        }

        .cart-summary {
            background: #fef5e7;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 18px;
            color: var(--dark-color);
            border-top: 2px solid #e1e5ee;
            padding-top: 10px;
            margin-top: 10px;
        }

        .checkout-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .checkout-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
        }

        .checkout-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .empty-cart {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-color);
        }

        .empty-cart i {
            font-size: 64px;
            margin-bottom: 15px;
            color: #ddd;
        }

        /* Checkout Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 24px;
            color: var(--dark-color);
            font-family: 'Georgia', serif;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-color);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
            font-size: 14px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5ee;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            background: white;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        /* Location Prediction Styles */
        .location-prediction {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #e1e5ee;
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 100;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: none;
        }

        .location-prediction.active {
            display: block;
        }

        .prediction-item {
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s;
        }

        .prediction-item:hover {
            background: #f8f9fa;
        }

        .prediction-item:last-child {
            border-bottom: none;
        }

        .prediction-main-text {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 4px;
        }

        .prediction-secondary-text {
            font-size: 12px;
            color: #666;
        }

        .location-loading {
            padding: 12px 15px;
            text-align: center;
            color: #666;
            font-style: italic;
        }

        .location-icon {
            margin-right: 10px;
            color: var(--primary-color);
        }

        /* Map Container */
        .map-container {
            height: 200px;
            border-radius: 10px;
            margin-top: 10px;
            border: 2px solid #e1e5ee;
            display: none;
        }

        .map-container.active {
            display: block;
        }

        /* Use Current Location Button */
        .use-location-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .use-location-btn:hover {
            background: #e76f51;
        }

        /* Messages */
        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        .success-message {
            background: #e6ffe6;
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .error-message {
            background: #ffe6e6;
            color: var(--error-color);
            border-left: 4px solid var(--error-color);
        }

        /* Order Confirmation */
        .order-confirmation {
            text-align: center;
            padding: 30px;
        }

        .order-confirmation i {
            font-size: 64px;
            color: var(--success-color);
            margin-bottom: 20px;
        }

        .order-number {
            font-size: 20px;
            font-weight: bold;
            color: var(--dark-color);
            margin-bottom: 15px;
        }

        .order-total {
            font-size: 24px;
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: var(--text-color);
            opacity: 0.7;
            font-size: 14px;
        }

        /* Delivery Options */
        .delivery-options {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .delivery-option {
            flex: 1;
            padding: 15px;
            border: 2px solid #e1e5ee;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .delivery-option:hover {
            border-color: var(--primary-color);
        }

        .delivery-option.active {
            border-color: var(--primary-color);
            background: #fef5e7;
        }

        .delivery-icon {
            font-size: 24px;
            margin-bottom: 8px;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Home Button -->
        <a href="index.php" class="home-button" title="Back to Home">
            <i class="fas fa-home"></i>
        </a>

        <!-- Header -->
        <div class="header">
            <div class="logo">
                <i class="fas fa-coffee"></i>
                <h1><?php echo $cafe_theme['cafe_name']; ?></h1>
                <p>Premium Beverages & Delicious Rice Meals</p>
            </div>
            
            <?php if (isset($success)): ?>
            <div class="message success-message">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
            <div class="message error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Navigation Tabs -->
        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showSection('menu')">
                <i class="fas fa-utensils"></i> Our Menu
            </button>
            <button class="nav-tab" onclick="showSection('cart')" id="cartTab">
                <i class="fas fa-shopping-cart"></i> My Cart 
                <?php if ($cart_count > 0): ?>
                <span class="cart-count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </button>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Menu Section -->
            <div class="menu-section" id="menuSection">
                <h2 class="section-title">
                    <i class="fas fa-mug-hot"></i> Cafe Iyah Menu
                </h2>
                
                <!-- Category Filter -->
                <div class="category-filter">
                    <button class="category-btn active" onclick="filterCategory('all')">All Items</button>
                    <button class="category-btn" onclick="filterCategory('Espresso')">Espresso</button>
                    <button class="category-btn" onclick="filterCategory('Frappe')">Frappe</button>
                    <button class="category-btn" onclick="filterCategory('Milk Tea')">Milk Tea</button>
                    <button class="category-btn" onclick="filterCategory('Fruit Soda')">Fruit Soda</button>
                    <button class="category-btn" onclick="filterCategory('Rice Meals')">Rice Meals</button>
                </div>

                <!-- Menu Grid -->
                <div class="menu-grid" id="menuGrid">
                    <?php foreach ($_SESSION['menu_items'] as $item): ?>
                    <div class="menu-item-card" data-category="<?php echo $item['category']; ?>">
                        <div class="menu-item-emoji"><?php echo $item['image']; ?></div>
                        <div class="menu-item-name"><?php echo $item['name']; ?></div>
                        <div class="menu-item-category"><?php echo $item['category']; ?></div>
                        <div class="menu-item-desc"><?php echo $item['description']; ?></div>
                        <div class="menu-item-footer">
                            <div class="menu-item-price">₱<?php echo number_format($item['price'], 0); ?></div>
                            <form method="POST" class="add-to-cart-form">
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="1" min="1" max="10" class="quantity-input">
                                <button type="submit" class="add-cart-btn">
                                    <i class="fas fa-cart-plus"></i> Add
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Cart Section -->
            <div class="cart-section" id="cartSection" style="display: none;">
                <div class="cart-header">
                    <h2 class="cart-title">My Order</h2>
                    <div class="cart-count"><?php echo $cart_count; ?></div>
                </div>

                <div class="cart-items">
                    <?php if (empty($_SESSION['cart'])): ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Your cart is empty</h3>
                        <p>Add some delicious items from our menu!</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="cart-item">
                            <div class="cart-item-info">
                                <div class="cart-item-name"><?php echo $item['name']; ?></div>
                                <div class="cart-item-price">₱<?php echo number_format($item['price'], 0); ?> each</div>
                            </div>
                            <div class="cart-item-controls">
                                <form method="POST" class="quantity-control">
                                    <input type="hidden" name="action" value="update_quantity">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="quantity" value="<?php echo $item['quantity'] - 1; ?>" class="qty-btn">-</button>
                                    <span style="padding: 0 10px; font-weight: 600;"><?php echo $item['quantity']; ?></span>
                                    <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" class="qty-btn">+</button>
                                </form>
                                <a href="?action=remove_from_cart&item_id=<?php echo $item['id']; ?>" class="remove-item" title="Remove item">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($_SESSION['cart'])): ?>
                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>₱<?php echo number_format($cart_total, 0); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (8%):</span>
                        <span>₱<?php echo number_format($tax, 0); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>₱<?php echo number_format($final_total, 0); ?></span>
                    </div>
                </div>

                <button class="checkout-btn" onclick="openCheckoutModal()">
                    <i class="fas fa-credit-card"></i> Proceed to Checkout
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Checkout Modal -->
        <div class="modal" id="checkoutModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Checkout & Delivery</h2>
                    <button class="close-modal" onclick="closeCheckoutModal()">&times;</button>
                </div>

                <form method="POST" id="checkoutForm">
                    <input type="hidden" name="action" value="checkout">
                    
                    <div class="form-group">
                        <label for="customer_name">Full Name *</label>
                        <input type="text" id="customer_name" name="customer_name" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="customer_phone">Phone Number *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" placeholder="Enter your phone number" required>
                    </div>

                    <!-- Delivery Options -->
                    <div class="delivery-options">
                        <div class="delivery-option active" onclick="selectDeliveryOption('delivery')">
                            <div class="delivery-icon">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <div>Delivery</div>
                        </div>
                        <div class="delivery-option" onclick="selectDeliveryOption('pickup')">
                            <div class="delivery-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <div>Pickup</div>
                        </div>
                    </div>

                    <div class="form-group" id="addressGroup">
                        <label for="customer_address">Delivery Address *</label>
                        <input type="text" id="customer_address" name="customer_address" placeholder="Start typing your address..." required>
                        <button type="button" class="use-location-btn" onclick="getCurrentLocation()">
                            <i class="fas fa-location-arrow"></i> Use Current Location
                        </button>
                        <div class="location-prediction" id="locationPrediction"></div>
                        <div class="map-container" id="addressMap"></div>
                    </div>

                    <div class="form-group">
                        <label for="payment_method">Payment Method *</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash on Delivery</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="digital">Digital Wallet</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="special_instructions">Special Instructions (Optional)</label>
                        <textarea id="special_instructions" name="special_instructions" rows="2" placeholder="Any special requests or instructions..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Order Summary</label>
                        <div style="background: #fef5e7; padding: 15px; border-radius: 8px;">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span><?php echo $item['quantity']; ?> x <?php echo $item['name']; ?></span>
                                <span>₱<?php echo number_format($item['price'] * $item['quantity'], 0); ?></span>
                            </div>
                            <?php endforeach; ?>
                            <hr style="margin: 10px 0;">
                            <div style="display: flex; justify-content: space-between; font-weight: bold;">
                                <span>Total Amount:</span>
                                <span>₱<?php echo number_format($final_total, 0); ?></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="checkout-btn">
                        <i class="fas fa-shopping-bag"></i> Place Order
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><?php echo $cafe_theme['cafe_name']; ?> &copy; 2024 | Premium Beverages & Rice Meals</p>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Navigation functions
        function showSection(section) {
            document.getElementById('menuSection').style.display = 'none';
            document.getElementById('cartSection').style.display = 'none';
            
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            if (section === 'menu') {
                document.getElementById('menuSection').style.display = 'block';
                document.querySelector('.nav-tab').classList.add('active');
            } else if (section === 'cart') {
                document.getElementById('cartSection').style.display = 'block';
                document.getElementById('cartTab').classList.add('active');
            }
        }

        // Category filter function
        function filterCategory(category) {
            const items = document.querySelectorAll('.menu-item-card');
            const buttons = document.querySelectorAll('.category-btn');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            items.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Checkout modal functions
        function openCheckoutModal() {
            document.getElementById('checkoutModal').classList.add('active');
        }

        function closeCheckoutModal() {
            document.getElementById('checkoutModal').classList.remove('active');
        }

        // Delivery option selection
        function selectDeliveryOption(option) {
            document.querySelectorAll('.delivery-option').forEach(opt => {
                opt.classList.remove('active');
            });
            event.target.closest('.delivery-option').classList.add('active');
            
            const addressGroup = document.getElementById('addressGroup');
            const addressInput = document.getElementById('customer_address');
            
            if (option === 'pickup') {
                addressGroup.style.display = 'none';
                addressInput.removeAttribute('required');
                addressInput.value = 'Store Pickup - ' + new Date().toLocaleTimeString();
            } else {
                addressGroup.style.display = 'block';
                addressInput.setAttribute('required', 'required');
                addressInput.value = '';
            }
        }

        // Location prediction variables
        let predictionTimeout;
        let map;
        let marker;

        // Initialize location prediction
        document.getElementById('customer_address').addEventListener('input', function(e) {
            clearTimeout(predictionTimeout);
            const query = e.target.value.trim();
            
            if (query.length < 3) {
                hidePredictions();
                return;
            }
            
            predictionTimeout = setTimeout(() => {
                searchLocations(query);
            }, 500);
        });

        // Hide predictions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#customer_address') && !e.target.closest('.location-prediction')) {
                hidePredictions();
            }
        });

        function hidePredictions() {
            document.getElementById('locationPrediction').classList.remove('active');
        }

        // Search locations using OpenStreetMap Nominatim API
        async function searchLocations(query) {
            const predictionContainer = document.getElementById('locationPrediction');
            predictionContainer.innerHTML = '<div class="location-loading">Searching locations...</div>';
            predictionContainer.classList.add('active');
            
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=ph`);
                const locations = await response.json();
                
                predictionContainer.innerHTML = '';
                
                if (locations.length === 0) {
                    predictionContainer.innerHTML = '<div class="location-loading">No locations found</div>';
                    return;
                }
                
                locations.forEach(location => {
                    const item = document.createElement('div');
                    item.className = 'prediction-item';
                    item.innerHTML = `
                        <div class="prediction-main-text">
                            <i class="fas fa-map-marker-alt location-icon"></i>
                            ${location.display_name}
                        </div>
                    `;
                    item.addEventListener('click', () => {
                        selectLocation(location);
                    });
                    predictionContainer.appendChild(item);
                });
            } catch (error) {
                predictionContainer.innerHTML = '<div class="location-loading">Error searching locations</div>';
                console.error('Location search error:', error);
            }
        }

        // Select a location from predictions
        function selectLocation(location) {
            const addressInput = document.getElementById('customer_address');
            addressInput.value = location.display_name;
            hidePredictions();
            showMap(location.lat, location.lon);
        }

        // Show map for selected location
        function showMap(lat, lon) {
            const mapContainer = document.getElementById('addressMap');
            mapContainer.classList.add('active');
            
            if (!map) {
                map = L.map('addressMap').setView([lat, lon], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
            } else {
                map.setView([lat, lon], 15);
            }
            
            if (marker) {
                map.removeLayer(marker);
            }
            
            marker = L.marker([lat, lon]).addTo(map)
                .bindPopup('Delivery Location')
                .openPopup();
        }

        // Get current location using geolocation API
        function getCurrentLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser');
                return;
            }
            
            const addressInput = document.getElementById('customer_address');
            addressInput.value = 'Getting your location...';
            
            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    
                    try {
                        // Reverse geocode to get address
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                        const data = await response.json();
                        
                        addressInput.value = data.display_name;
                        showMap(lat, lon);
                    } catch (error) {
                        addressInput.value = `Near ${lat.toFixed(4)}, ${lon.toFixed(4)}`;
                        showMap(lat, lon);
                        console.error('Reverse geocoding error:', error);
                    }
                },
                (error) => {
                    console.error('Geolocation error:', error);
                    addressInput.value = '';
                    alert('Unable to get your current location. Please enter your address manually.');
                }
            );
        }

        // Close modal when clicking outside
        document.getElementById('checkoutModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCheckoutModal();
            }
        });

        // Auto-hide messages after 5 seconds
        setTimeout(() => {
            const messages = document.querySelectorAll('.message');
            messages.forEach(msg => {
                if (msg) msg.style.display = 'none';
            });
        }, 5000);

        // Add smooth animations for cart updates
        document.addEventListener('DOMContentLoaded', function() {
            const cartButtons = document.querySelectorAll('.add-cart-btn');
            cartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });
        });
    </script>
</body>
</html>