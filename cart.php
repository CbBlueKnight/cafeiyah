<?php include 'firebase-config.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Brew & Bean Café</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="brown darken-2">
        <div class="nav-wrapper container">
            <a href="index.php" class="brand-logo">Brew & Bean</a>
            <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down">
                <li><a href="index.php">Home</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li class="active"><a href="cart.php" class="cart-link">
                    <i class="material-icons left cart-icon">shopping_cart</i>
                    Cart 
                    <?php if (count($_SESSION['cart']) > 0): ?>
                        <span class="cart-badge"><?php echo count($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a></li>
            </ul>
        </div>
    </nav>

    <ul class="sidenav" id="mobile-demo">
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="cart.php">Cart</a></li>
    </ul>

    <!-- Cart Section -->
    <div class="container">
        <div class="section">
            <h3 class="center-align brown-text">Your Cart</h3>
            
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="center-align empty-cart">
                    <i class="material-icons large brown-text">shopping_cart</i>
                    <h5>Your cart is empty</h5>
                    <p>Browse our menu and add some delicious items!</p>
                    <div class="section">
                        <a href="menu.php" class="btn brown waves-effect waves-light">
                            <i class="material-icons left">local_cafe</i>
                            View Menu
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col s12 m8">
                        <!-- Cart Items -->
                        <div class="card hoverable">
                            <div class="card-content">
                                <div class="card-title">
                                    Shopping Cart
                                    <a href="clear-cart.php" class="btn red waves-effect waves-light right" id="clear-cart">
                                        <i class="material-icons left">delete_sweep</i>
                                        Clear Cart
                                    </a>
                                </div>
                                
                                <table class="striped highlight responsive-table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total = 0;
                                        $itemCount = 0;
                                        foreach ($_SESSION['cart'] as $index => $item): 
                                            $itemTotal = $item['price'] * $item['quantity'];
                                            $total += $itemTotal;
                                            $itemCount += $item['quantity'];
                                        ?>
                                            <tr class="cart-item" data-index="<?php echo $index; ?>">
                                                <td>
                                                    <div class="cart-item-info">
                                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                        <br>
                                                        <small class="grey-text">Item ID: <?php echo $item['id']; ?></small>
                                                    </div>
                                                </td>
                                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                                <td>
                                                    <div class="quantity-controls">
                                                        <a href="update-cart.php?index=<?php echo $index; ?>&quantity=<?php echo max(1, $item['quantity'] - 1); ?>" 
                                                           class="btn-flat btn-small quantity-btn minus">
                                                            <i class="material-icons">remove</i>
                                                        </a>
                                                        <span class="quantity-display"><?php echo $item['quantity']; ?></span>
                                                        <a href="update-cart.php?index=<?php echo $index; ?>&quantity=<?php echo $item['quantity'] + 1; ?>" 
                                                           class="btn-flat btn-small quantity-btn plus">
                                                            <i class="material-icons">add</i>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="item-total">$<?php echo number_format($itemTotal, 2); ?></td>
                                                <td>
                                                    <a href="remove-from-cart.php?index=<?php echo $index; ?>" 
                                                       class="btn-flat red-text remove-item tooltipped"
                                                       data-name="<?php echo htmlspecialchars($item['name']); ?>"
                                                       data-tooltip="Remove item">
                                                        <i class="material-icons">delete</i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Continue Shopping -->
                        <div class="section">
                            <a href="menu.php" class="btn-flat brown-text waves-effect">
                                <i class="material-icons left">arrow_back</i>
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                    
                    <div class="col s12 m4">
                        <!-- Order Summary -->
                        <div class="card hoverable sticky-summary">
                            <div class="card-content">
                                <span class="card-title">Order Summary</span>
                                <div class="divider"></div>
                                
                                <div class="section summary-details">
                                    <div class="summary-row">
                                        <span>Items (<?php echo $itemCount; ?>):</span>
                                        <span>$<?php echo number_format($total, 2); ?></span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Delivery Fee:</span>
                                        <span>$2.50</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Tax (8.5%):</span>
                                        <span>$<?php echo number_format($total * 0.085, 2); ?></span>
                                    </div>
                                    <div class="divider"></div>
                                    <div class="summary-row total-row">
                                        <strong>Total:</strong>
                                        <strong>$<?php echo number_format($total + 2.50 + ($total * 0.085), 2); ?></strong>
                                    </div>
                                </div>
                                
                                <div class="section">
                                    <a href="order.php" class="btn brown waves-effect waves-light btn-large full-width">
                                        <i class="material-icons right">arrow_forward</i>
                                        Proceed to Checkout
                                    </a>
                                </div>
                                
                                <div class="section">
                                    <div class="card-panel green lighten-5 green-text">
                                        <i class="material-icons left">local_shipping</i>
                                        Free delivery on orders over $25!
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card hoverable">
                            <div class="card-content">
                                <span class="card-title">Quick Actions</span>
                                <div class="section">
                                    <a href="menu.php?category=coffee" class="btn brown lighten-2 waves-effect waves-light full-width">
                                        <i class="material-icons left">local_cafe</i>
                                        Add Coffee
                                    </a>
                                </div>
                                <div class="section">
                                    <a href="menu.php?category=pastry" class="btn brown lighten-1 waves-effect waves-light full-width">
                                        <i class="material-icons left">cake</i>
                                        Add Pastry
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="page-footer brown darken-3">
        <div class="container">
            <div class="row">
                <div class="col l6 s12">
                    <h5 class="white-text">Brew & Bean Café</h5>
                    <p class="grey-text text-lighten-4">Serving quality coffee and delicious treats since 2010.</p>
                </div>
                <div class="col l4 offset-l2 s12">
                    <h5 class="white-text">Quick Links</h5>
                    <ul>
                        <li><a class="grey-text text-lighten-3" href="index.php">Home</a></li>
                        <li><a class="grey-text text-lighten-3" href="menu.php">Menu</a></li>
                        <li><a class="grey-text text-lighten-3" href="cart.php">Cart</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-copyright brown darken-4">
            <div class="container">
                © 2024 Brew & Bean Café
            </div>
        </div>
    </footer>

    <!-- Materialize JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('.sidenav');
            var instances = M.Sidenav.init(elems);
            
            var tooltips = document.querySelectorAll('.tooltipped');
            var instances = M.Tooltip.init(tooltips);

            // Cart specific functionality
            const clearCartBtn = document.getElementById('clear-cart');
            if (clearCartBtn) {
                clearCartBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to clear your entire cart? This action cannot be undone.')) {
                        window.location.href = this.href;
                    }
                });
            }

            // Add animation to quantity changes
            document.querySelectorAll('.quantity-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Add loading state
                    const row = this.closest('.cart-item');
                    row.style.opacity = '0.7';
                    
                    // Navigate to update URL
                    setTimeout(() => {
                        window.location.href = this.href;
                    }, 300);
                });
            });

            // Update cart totals in real-time (for future enhancement)
            function updateCartTotals() {
                // This would be used for real-time updates without page reload
                console.log('Cart totals would be updated here');
            }

            // Make summary card sticky on scroll
            window.addEventListener('scroll', function() {
                const summary = document.querySelector('.sticky-summary');
                if (summary) {
                    const scrollTop = window.pageYOffset;
                    if (scrollTop > 200) {
                        summary.style.position = 'sticky';
                        summary.style.top = '20px';
                    } else {
                        summary.style.position = 'relative';
                        summary.style.top = '0';
                    }
                }
            });
        });
    </script>
</body>
</html>