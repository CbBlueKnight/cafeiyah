<?php 
include 'firebase-config.php';

// Get order ID from URL or session
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

// If no order ID in URL, check if we have a recent order in session
if (empty($order_id) && isset($_SESSION['last_order_id'])) {
    $order_id = $_SESSION['last_order_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Brew & Bean Café</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="brown darken-2">
        <div class="nav-wrapper container">
            <a href="index.php" class="brand-logo">Brew & Bean</a>
        </div>
    </nav>

    <!-- Confirmation Section -->
    <div class="container">
        <div class="section">
            <div class="row">
                <div class="col s12 m8 offset-m2">
                    <div class="card-panel green lighten-5 center-align confirmation-card">
                        <i class="material-icons large green-text">check_circle</i>
                        <h4>Order Confirmed!</h4>
                        <p class="flow-text">Thank you for your order!</p>
                        
                        <?php if (!empty($order_id)): ?>
                            <div class="section order-details">
                                <div class="order-info">
                                    <p><strong>Order ID:</strong> <span class="order-id"><?php echo htmlspecialchars($order_id); ?></span></p>
                                    <p><strong>Order Date:</strong> <?php echo date('F j, Y \a\t g:i A'); ?></p>
                                    <p><strong>Estimated Delivery:</strong> 30-45 minutes</p>
                                </div>
                                
                                <div class="divider"></div>
                                
                                <div class="section">
                                    <h6>What's Next?</h6>
                                    <ul class="left-align">
                                        <li><i class="material-icons green-text">check</i> We've sent a confirmation email with your order details</li>
                                        <li><i class="material-icons green-text">check</i> Your order is being prepared</li>
                                        <li><i class="material-icons green-text">check</i> You'll receive a notification when your order is out for delivery</li>
                                    </ul>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="section">
                                <p>Your order has been received and is being processed.</p>
                                <p>If you have any questions, please contact us at (555) 123-4567.</p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="section action-buttons">
                            <a href="index.php" class="btn brown waves-effect waves-light">
                                <i class="material-icons left">home</i>
                                Back to Home
                            </a>
                            <a href="menu.php" class="btn-flat brown-text waves-effect">
                                <i class="material-icons left">local_cafe</i>
                                Order More
                            </a>
                            <?php if (!empty($order_id)): ?>
                                <a href="order-tracking.php?order_id=<?php echo urlencode($order_id); ?>" class="btn-flat blue-text waves-effect">
                                    <i class="material-icons left">track_changes</i>
                                    Track Order
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Order Summary (if cart data is still available) -->
                        <?php if (!empty($_SESSION['last_order_items'])): ?>
                            <div class="section order-summary">
                                <h6>Order Summary</h6>
                                <div class="card-panel">
                                    <?php 
                                    $total = 0;
                                    foreach ($_SESSION['last_order_items'] as $item): 
                                        $itemTotal = $item['price'] * $item['quantity'];
                                        $total += $itemTotal;
                                    ?>
                                        <div class="order-item-summary">
                                            <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                                            <span>$<?php echo number_format($itemTotal, 2); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="divider"></div>
                                    <div class="order-total">
                                        <strong>Total: $<?php echo number_format($total + 2.50 + ($total * 0.085), 2); ?></strong>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Info Section -->
    <div class="container">
        <div class="section">
            <div class="row">
                <div class="col s12 m4">
                    <div class="card-panel center-align hoverable">
                        <i class="material-icons large brown-text">local_shipping</i>
                        <h6>Delivery Information</h6>
                        <p>Your order will be delivered within 30-45 minutes</p>
                    </div>
                </div>
                <div class="col s12 m4">
                    <div class="card-panel center-align hoverable">
                        <i class="material-icons large brown-text">support_agent</i>
                        <h6>Need Help?</h6>
                        <p>Contact us: (555) 123-4567</p>
                    </div>
                </div>
                <div class="col s12 m4">
                    <div class="card-panel center-align hoverable">
                        <i class="material-icons large brown-text">loyalty</i>
                        <h6>Loyalty Points</h6>
                        <p>You've earned <?php echo isset($total) ? floor($total) : 0; ?> points!</p>
                    </div>
                </div>
            </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add celebration effect
            const confetti = () => {
                const colors = ['#5d4037', '#8d6e63', '#d7ccc8', '#3e2723'];
                for (let i = 0; i < 50; i++) {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.cssText = `
                        position: fixed;
                        width: 10px;
                        height: 10px;
                        background: ${colors[Math.floor(Math.random() * colors.length)]};
                        top: -10px;
                        left: ${Math.random() * 100}%;
                        animation: fall linear forwards;
                        animation-duration: ${Math.random() * 3 + 2}s;
                        z-index: 9999;
                        border-radius: 50%;
                    `;
                    document.body.appendChild(confetti);
                    
                    setTimeout(() => confetti.remove(), 5000);
                }
            };

            // Add confetti style
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fall {
                    to {
                        transform: translateY(100vh) rotate(${Math.random() * 360}deg);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);

            // Trigger confetti
            setTimeout(confetti, 500);
            
            // Print order functionality
            const printOrder = () => {
                window.print();
            };

            // Add print button event listener if needed
            document.getElementById('print-order')?.addEventListener('click', printOrder);
        });
    </script>
</body>
</html>