<?php include 'firebase-config.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Brew & Bean Café</title>
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
                <li><a href="cart.php" class="cart-link">
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

    <!-- Checkout Section -->
    <div class="container">
        <div class="section">
            <h3 class="center-align brown-text">Checkout</h3>
            
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="center-align">
                    <i class="material-icons large brown-text">shopping_cart</i>
                    <h5>Your cart is empty</h5>
                    <p>Please add items to your cart before checking out.</p>
                    <a href="menu.php" class="btn brown waves-effect waves-light">
                        <i class="material-icons left">local_cafe</i>
                        View Menu
                    </a>
                </div>
            <?php else: ?>
                <!-- Checkout Progress -->
                <div class="row">
                    <div class="col s12">
                        <ul class="stepper horizontal">
                            <li class="step active">
                                <div class="step-title waves-effect">Cart</div>
                            </li>
                            <li class="step active">
                                <div class="step-title waves-effect">Information</div>
                            </li>
                            <li class="step">
                                <div class="step-title">Payment</div>
                            </li>
                            <li class="step">
                                <div class="step-title">Confirmation</div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col s12 m8">
                        <!-- Customer Information Form -->
                        <div class="card hoverable">
                            <div class="card-content">
                                <span class="card-title">
                                    <i class="material-icons left">person</i>
                                    Customer Information
                                </span>
                                
                                <form id="checkout-form" action="process-order.php" method="POST" novalidate>
                                    <div class="row">
                                        <div class="input-field col s12 m6">
                                            <input id="first_name" name="first_name" type="text" class="validate" required 
                                                   pattern="[A-Za-z ]{2,}" title="Please enter at least 2 characters">
                                            <label for="first_name">First Name *</label>
                                            <span class="helper-text" data-error="Please enter a valid first name"></span>
                                        </div>
                                        <div class="input-field col s12 m6">
                                            <input id="last_name" name="last_name" type="text" class="validate" required
                                                   pattern="[A-Za-z ]{2,}" title="Please enter at least 2 characters">
                                            <label for="last_name">Last Name *</label>
                                            <span class="helper-text" data-error="Please enter a valid last name"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="input-field col s12 m6">
                                            <input id="email" name="email" type="email" class="validate" required>
                                            <label for="email">Email Address *</label>
                                            <span class="helper-text" data-error="Please enter a valid email address"></span>
                                        </div>
                                        <div class="input-field col s12 m6">
                                            <input id="phone" name="phone" type="tel" class="validate" required
                                                   pattern="[0-9+\-() ]{10,}" title="Please enter a valid phone number">
                                            <label for="phone">Phone Number *</label>
                                            <span class="helper-text" data-error="Please enter a valid phone number"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <textarea id="address" name="address" class="materialize-textarea validate" required
                                                      minlength="10" title="Please enter your complete address"></textarea>
                                            <label for="address">Delivery Address *</label>
                                            <span class="helper-text" data-error="Please enter a complete address"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="input-field col s12 m6">
                                            <input id="city" name="city" type="text" class="validate" required>
                                            <label for="city">City *</label>
                                            <span class="helper-text" data-error="Please enter your city"></span>
                                        </div>
                                        <div class="input-field col s12 m3">
                                            <input id="zipcode" name="zipcode" type="text" class="validate" required
                                                   pattern="[0-9]{5}" title="Please enter a 5-digit ZIP code">
                                            <label for="zipcode">ZIP Code *</label>
                                            <span class="helper-text" data-error="Please enter a valid ZIP code"></span>
                                        </div>
                                        <div class="input-field col s12 m3">
                                            <select id="delivery_time" name="delivery_time" required>
                                                <option value="" disabled selected>Choose time</option>
                                                <option value="asap">ASAP (30-45 min)</option>
                                                <option value="10:00">10:00 AM</option>
                                                <option value="11:00">11:00 AM</option>
                                                <option value="12:00">12:00 PM</option>
                                                <option value="13:00">1:00 PM</option>
                                                <option value="14:00">2:00 PM</option>
                                                <option value="15:00">3:00 PM</option>
                                            </select>
                                            <label>Delivery Time *</label>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <textarea id="special_instructions" name="special_instructions" class="materialize-textarea"
                                                      placeholder="Any special delivery instructions? (e.g., gate code, leave at door)"></textarea>
                                            <label for="special_instructions">Special Instructions (Optional)</label>
                                        </div>
                                    </div>
                                    
                                    <!-- Payment Method (Simplified for demo) -->
                                    <div class="row">
                                        <div class="col s12">
                                            <div class="card-panel grey lighten-4">
                                                <h6>Payment Method</h6>
                                                <p>
                                                    <label>
                                                        <input name="payment_method" type="radio" value="cash" checked />
                                                        <span>Cash on Delivery</span>
                                                    </label>
                                                </p>
                                                <p class="grey-text">Credit card payment coming soon!</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col s12">
                                            <p>
                                                <label>
                                                    <input type="checkbox" id="terms" name="terms" required />
                                                    <span>I agree to the <a href="#terms-modal" class="modal-trigger">terms and conditions</a> *</span>
                                                </label>
                                            </p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col s12 m4">
                        <!-- Order Summary -->
                        <div class="card hoverable sticky-summary">
                            <div class="card-content">
                                <span class="card-title">Order Summary</span>
                                <div class="divider"></div>
                                
                                <div class="section">
                                    <?php 
                                    $subtotal = 0;
                                    $itemCount = 0;
                                    foreach ($_SESSION['cart'] as $item): 
                                        $itemTotal = $item['price'] * $item['quantity'];
                                        $subtotal += $itemTotal;
                                        $itemCount += $item['quantity'];
                                    ?>
                                        <div class="order-item">
                                            <div class="order-item-name">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                                <span class="quantity-badge">x<?php echo $item['quantity']; ?></span>
                                            </div>
                                            <div class="order-item-price">$<?php echo number_format($itemTotal, 2); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="divider"></div>
                                <div class="section summary-details">
                                    <div class="summary-row">
                                        <span>Subtotal (<?php echo $itemCount; ?> items):</span>
                                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Delivery Fee:</span>
                                        <span>$2.50</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Tax (8.5%):</span>
                                        <span>$<?php echo number_format($subtotal * 0.085, 2); ?></span>
                                    </div>
                                    <div class="divider"></div>
                                    <div class="summary-row total-row">
                                        <strong>Total:</strong>
                                        <strong>$<?php echo number_format($subtotal + 2.50 + ($subtotal * 0.085), 2); ?></strong>
                                    </div>
                                </div>
                                
                                <div class="section">
                                    <button type="submit" form="checkout-form" class="btn brown waves-effect waves-light btn-large full-width" id="place-order-btn">
                                        <i class="material-icons right">check</i>
                                        Place Order
                                    </button>
                                </div>
                                
                                <div class="section">
                                    <a href="cart.php" class="btn-flat brown-text waves-effect full-width">
                                        <i class="material-icons left">arrow_back</i>
                                        Back to Cart
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Tracking Section -->
                <div class="row">
                    <div class="col s12">
                        <div class="card-panel">
                            <h5>Track Your Order</h5>
                            <p>Want to check on an existing order?</p>
                            <div class="input-field">
                                <input type="text" id="track-order-id" placeholder="Enter your order ID">
                                <button class="btn brown waves-effect waves-light" id="track-order-btn">
                                    <i class="material-icons left">search</i>
                                    Track Order
                                </button>
                            </div>
                            <div id="order-status"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Terms Modal -->
    <div id="terms-modal" class="modal">
        <div class="modal-content">
            <h4>Terms and Conditions</h4>
            <p>By placing an order, you agree to our terms of service...</p>
            <!-- Add full terms content here -->
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close btn brown">I Understand</a>
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
            
            var modals = document.querySelectorAll('.modal');
            var instances = M.Modal.init(modals);
            
            var selects = document.querySelectorAll('select');
            var instances = M.FormSelect.init(selects);

            // Form validation
            const form = document.getElementById('checkout-form');
            const placeOrderBtn = document.getElementById('place-order-btn');

            if (form && placeOrderBtn) {
                placeOrderBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Validate form
                    let isValid = true;
                    const requiredFields = form.querySelectorAll('[required]');
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('invalid');
                        } else {
                            field.classList.remove('invalid');
                            field.classList.add('valid');
                        }
                    });

                    // Special validation for terms checkbox
                    const termsCheckbox = document.getElementById('terms');
                    if (!termsCheckbox.checked) {
                        isValid = false;
                        termsCheckbox.classList.add('invalid');
                    }

                    if (isValid) {
                        // Show loading state
                        placeOrderBtn.classList.add('disabled');
                        placeOrderBtn.innerHTML = '<i class="material-icons left">hourglass_empty</i> Processing...';
                        
                        // Submit form
                        setTimeout(() => {
                            form.submit();
                        }, 1000);
                    } else {
                        M.toast({html: 'Please fill in all required fields correctly.', classes: 'red'});
                    }
                });
            }

            // Real-time form validation
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.classList.add('invalid');
                    } else {
                        this.classList.remove('invalid');
                        this.classList.add('valid');
                    }
                });
                
                input.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('invalid');
                        this.classList.add('valid');
                    }
                });
            });
        });
    </script>
</body>
</html>