<?php
session_start();
// Sample products data
$products = [
    [
        'id' => 1,
        'name' => 'Espresso',
        'price' => 3.50,
        'category' => 'coffee',
        'description' => 'Strong and rich espresso shot',
        'image' => 'local_cafe'
    ],
    [
        'id' => 2,
        'name' => 'Cappuccino',
        'price' => 4.50,
        'category' => 'coffee',
        'description' => 'Perfect blend of espresso and steamed milk',
        'image' => 'local_cafe'
    ],
    [
        'id' => 3,
        'name' => 'Croissant',
        'price' => 2.75,
        'category' => 'pastry',
        'description' => 'Buttery and flaky French croissant',
        'image' => 'cake'
    ],
    [
        'id' => 4,
        'name' => 'Green Tea',
        'price' => 3.25,
        'category' => 'tea',
        'description' => 'Refreshing green tea leaves',
        'image' => 'emoji_food_beverage'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Brew & Bean Café</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .product-card {
            margin-bottom: 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .product-image {
            height: 120px;
            background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8d6e63;
        }
        .add-to-cart-btn {
            border-radius: 25px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Navigation (same as cart.php) -->
    <nav class="brown darken-2">
        <div class="nav-wrapper container">
            <a href="index.php" class="brand-logo">Brew & Bean</a>
            <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down">
                <li><a href="index.php">Home</a></li>
                <li class="active"><a href="menu.php">Menu</a></li>
                <li><a href="cart.php" class="cart-link">
                    <i class="material-icons left">shopping_cart</i>
                    Cart 
                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-badge"><?php echo count($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="section">
            <h3 class="center-align brown-text">Our Menu</h3>
            
            <div class="row">
                <?php foreach ($products as $product): ?>
                <div class="col s12 m6 l4">
                    <div class="card product-card hoverable">
                        <div class="card-content">
                            <div class="product-image">
                                <i class="material-icons"><?php echo $product['image']; ?></i>
                            </div>
                            <span class="card-title"><?php echo $product['name']; ?></span>
                            <p><?php echo $product['description']; ?></p>
                            <div class="price-section" style="margin: 15px 0;">
                                <strong class="brown-text" style="font-size: 1.2rem;">
                                    $<?php echo number_format($product['price'], 2); ?>
                                </strong>
                            </div>
                            
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
                                <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                                <input type="hidden" name="product_category" value="<?php echo $product['category']; ?>">
                                <input type="hidden" name="product_image" value="<?php echo $product['image']; ?>">
                                
                                <div class="input-field">
                                    <select name="quantity" class="browser-default">
                                        <option value="1" selected>1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                                
                                <div class="input-field">
                                    <input type="text" name="customizations" placeholder="Any special instructions?">
                                </div>
                                
                                <button type="submit" name="add_to_cart" class="btn brown waves-effect waves-light add-to-cart-btn full-width">
                                    <i class="material-icons left">add_shopping_cart</i>
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>