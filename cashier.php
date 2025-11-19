<?php
session_start();

// Check if user is logged in and is cashier
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'cashier') {
    header('Location: admin.php');
    exit;
}

$current_user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe POS - Cashier</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/cashier.css">
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>
</head>
<body>
    <!-- Cashier Fullscreen Dashboard -->
    <div class="cashier-dashboard">
        <!-- Left Panel - Order Summary -->
        <div class="order-panel">
            <!-- Header with Logout Button -->
            <div class="order-header">
                <div class="header-top">
                    <button class="logout-btn-small" onclick="handleLogout()" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                    <div class="header-info">
                        <h2>Current Order</h2>
                        <p><?php echo $current_user['name']; ?> | <?php echo date('M j, Y g:i A'); ?></p>
                        <div class="staff-status-section">
                            <span class="online-indicator online"></span>
                            <span class="status-badge status-active">Active</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="current-order">
                <div class="order-items" id="orderItems">
                    <div style="text-align: center; color: #666; padding: 40px 20px;">
                        <i class="fas fa-shopping-cart" style="font-size: 3em; margin-bottom: 15px; opacity: 0.3;"></i>
                        <p>No items in order</p>
                    </div>
                </div>
            </div>
            
            <div class="order-total">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">₱0.00</span>
                </div>
                <div class="total-row">
                    <span>Tax (12%):</span>
                    <span id="tax">₱0.00</span>
                </div>
                <div class="total-row grand-total">
                    <span>Total:</span>
                    <span id="grandTotal">₱0.00</span>
                </div>
                
                <div class="action-buttons">
                    <button class="action-btn clear-btn" onclick="clearOrder()">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                    <button class="action-btn checkout-btn" onclick="processOrder()">
                        <i class="fas fa-credit-card"></i> Checkout
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Product Selection -->
        <div class="products-panel">
            <!-- Enhanced Categories Header -->
            <div class="categories-header">
                <div class="categories-title">
                    <h2><i class="fas fa-list"></i> Menu Categories</h2>
                    <span class="categories-subtitle">Click to browse products</span>
                </div>
            </div>
            
            <!-- Loading State -->
            <div id="loadingState" class="loading-overlay">
                <div style="text-align: center;">
                    <div class="loading-spinner" style="width: 40px; height: 40px; border-width: 4px; margin-bottom: 15px;"></div>
                    <p>Loading menu from Firebase...</p>
                </div>
            </div>
            
            <!-- Enhanced Category Buttons -->
            <div class="categories-nav" id="categoriesNav">
                <div class="category-buttons-container" id="categoryButtons">
                    <!-- Category buttons will be populated by JavaScript -->
                </div>
            </div>
            
            <!-- Category Sections -->
            <div class="products-container" id="productsContainer">
                <!-- All sections will be populated by JavaScript -->
            </div>

            <!-- Error State -->
            <div id="errorState" class="loading-overlay">
                <div style="text-align: center; color: var(--danger-color);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3em; margin-bottom: 15px;"></i>
                    <h3>Failed to Load Menu</h3>
                    <p>Unable to connect to Firebase. Please refresh the page.</p>
                    <button onclick="location.reload()" style="margin-top: 15px; padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">
                        Retry
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Modal -->
    <div id="paymentModal" class="modal-overlay">
        <div class="modal-content payment-modal">
            <div class="modal-header">
                <h3>Select Payment Method</h3>
                <button class="close-btn" onclick="closePaymentModal()">&times;</button>
            </div>
            <div class="payment-options">
                <div class="payment-option" onclick="selectPaymentMethod('cash')">
                    <div class="payment-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="payment-info">
                        <h4>Cash</h4>
                        <p>Pay with cash</p>
                    </div>
                    <div class="payment-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
                
                <div class="payment-option" onclick="selectPaymentMethod('credit_card')">
                    <div class="payment-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="payment-info">
                        <h4>Credit Card</h4>
                        <p>Pay with card</p>
                    </div>
                    <div class="payment-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
                
                <div class="payment-option" onclick="selectPaymentMethod('qr')">
                    <div class="payment-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div class="payment-info">
                        <h4>QR Code</h4>
                        <p>Scan to pay</p>
                    </div>
                    <div class="payment-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closePaymentModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Cash Payment Modal -->
    <div id="cashModal" class="modal-overlay">
        <div class="modal-content cash-modal">
            <div class="modal-header">
                <h3>Cash Payment</h3>
                <button class="close-btn" onclick="closeCashModal()">&times;</button>
            </div>
            <div class="payment-details">
                <div class="amount-section">
                    <div class="amount-row">
                        <span>Total Amount:</span>
                        <span id="totalAmountDisplay">₱0.00</span>
                    </div>
                    <div class="amount-row">
                        <label for="cashAmount">Amount Received:</label>
                        <input type="number" id="cashAmount" placeholder="0.00" step="0.01" min="0" oninput="calculateChange()">
                    </div>
                    <div class="amount-row change-row">
                        <span>Change:</span>
                        <span id="changeAmount">₱0.00</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeCashModal()">Back</button>
                <button class="btn-primary" id="confirmCashPayment" onclick="confirmCashPayment()" disabled>
                    Confirm Payment
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Processing Modal -->
    <div id="processingModal" class="modal-overlay">
        <div class="modal-content processing-modal">
            <div class="processing-content">
                <div class="loading-spinner-large"></div>
                <h3>Processing Payment...</h3>
                <p>Please wait while we process your payment</p>
            </div>
        </div>
    </div>

    <!-- Payment Success Modal -->
    <div id="successModal" class="modal-overlay">
        <div class="modal-content success-modal">
            <div class="success-content">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Payment Successful!</h3>
                <p id="successMessage">Order has been processed successfully</p>
                <button class="btn-primary" onclick="closeSuccessModal()">Continue</button>
            </div>
        </div>
    </div>

    <script>
        // Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyCSRi9IyNkK6DA6YYfnAdzI9LigkgTVG24",
            authDomain: "cafe-iyah-5869e.firebaseapp.com",
            databaseURL: "https://cafe-iyah-5869e-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "cafe-iyah-5869e",
            storageBucket: "cafe-iyah-5869e.firebasestorage.app",
            messagingSenderId: "737248847652",
            appId: "1:737248847652:web:f7ed666e68ca3dd4e975b1",
            measurementId: "G-ZKF5NMVYH6"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        const db = firebase.firestore();

        // Cashier functionality variables
        let currentOrder = [];
        let firebaseMenuItems = [];

        // Enhanced function to fetch menu items from Firebase
        async function fetchMenuItems() {
            console.log("Fetching menu items from Firebase...");
            
            try {
                const snapshot = await db.collection('menu_items').get();
                console.log("Firebase query completed. Documents found:", snapshot.size);
                
                if (snapshot.empty) {
                    console.log("No menu items found in Firebase Firestore");
                    showErrorState();
                    return;
                }
                
                firebaseMenuItems = snapshot.docs.map(doc => {
                    const data = doc.data();
                    console.log("Processing item:", doc.id, data);
                    return {
                        id: doc.id,
                        ...data
                    };
                });
                
                console.log("Final firebaseMenuItems array:", firebaseMenuItems);
                
                // Hide loading state and show content
                hideLoadingState();
                
                // Update the UI with Firebase data
                updateCategoryButtons();
                updateMenuDisplay();
                
            } catch (error) {
                console.error('Error fetching menu items:', error);
                showErrorState();
            }
        }

        function hideLoadingState() {
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('categoriesNav').style.display = 'block';
            document.getElementById('productsContainer').style.display = 'block';
        }

        function showErrorState() {
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('categoriesNav').style.display = 'none';
            document.getElementById('productsContainer').style.display = 'none';
            document.getElementById('errorState').style.display = 'flex';
        }

        // Update category buttons dynamically
        function updateCategoryButtons() {
            const categories = getCategoriesFromMenu();
            const categoryButtons = document.getElementById('categoryButtons');
            
            let buttonsHTML = `
                <button class="category-btn-large active" onclick="filterProducts('all')" data-category="all">
                    <div class="category-icon">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <div class="category-text">
                        <span class="category-name">All Items</span>
                        <span class="category-count">${firebaseMenuItems.length}</span>
                    </div>
                </button>
            `;
            
            categories.forEach(category => {
                const count = firebaseMenuItems.filter(item => item.category === category).length;
                const categoryId = category.toLowerCase().replace(' ', '-');
                const icon = getCategoryIcon(category);
                
                buttonsHTML += `
                    <button class="category-btn-large" onclick="filterProducts('${category}')" data-category="${categoryId}">
                        <div class="category-icon">
                            <i class="fas fa-${icon}"></i>
                        </div>
                        <div class="category-text">
                            <span class="category-name">${category}</span>
                            <span class="category-count">${count}</span>
                        </div>
                    </button>
                `;
            });
            
            categoryButtons.innerHTML = buttonsHTML;
        }

        function getCategoriesFromMenu() {
            const categories = [...new Set(firebaseMenuItems.map(item => item.category))];
            return categories.filter(category => category);
        }

        function getCategoryIcon(category) {
            const iconMap = {
                'Espresso': 'coffee',
                'Frappe': 'glass-whiskey',
                'Milk Tea': 'mug-hot',
                'Food': 'utensils'
            };
            return iconMap[category] || 'cube';
        }

        // Update menu display with Firebase data
        function updateMenuDisplay() {
            const productsContainer = document.getElementById('productsContainer');
            
            // Create All Items section
            let sectionsHTML = createCategorySection('all', 'All Menu Items', 'th-large', firebaseMenuItems);
            
            // Create category-specific sections
            const categories = getCategoriesFromMenu();
            categories.forEach(category => {
                const categoryItems = firebaseMenuItems.filter(item => item.category === category);
                const categoryId = category.toLowerCase().replace(' ', '-');
                const icon = getCategoryIcon(category);
                const description = getCategoryDescription(category);
                
                sectionsHTML += createCategorySection(categoryId, category, icon, categoryItems, description);
            });
            
            productsContainer.innerHTML = sectionsHTML;
        }

        function getCategoryDescription(category) {
            const descriptions = {
                'Espresso': 'coffee drinks',
                'Frappe': 'blended drinks',
                'Milk Tea': 'tea drinks',
                'Food': 'food items'
            };
            return descriptions[category] || 'items';
        }

        function createCategorySection(categoryId, categoryName, icon, items, description = 'products') {
            const cssCategoryId = categoryId.replace(/[\s-]/g, '').toLowerCase();
            const headerClass = `${cssCategoryId}-header`;
            const count = items.length;
            
            return `
                <div class="category-section ${categoryId === 'all' ? 'active' : ''}" id="category-${categoryId}">
                    <div class="section-header ${headerClass}">
                        <h3><i class="fas fa-${icon}"></i> ${categoryName}</h3>
                        <span class="category-count-badge">${count} ${description} available</span>
                    </div>
                    <div class="products-grid">
                        ${items.map(item => `
                            <div class="product-card ${item.stock <= 0 ? 'out-of-stock' : ''}" 
                                 onclick="addToOrder('${item.id}')"
                                 data-category="${item.category}">
                                <div class="product-emoji">${item.image}</div>
                                <div class="product-name">${item.name}</div>
                                <div class="product-price">₱${item.price.toFixed(2)}</div>
                                <div class="product-description">${item.description}</div>
                                <div class="product-category-badge category-${item.category.toLowerCase().replace(/[\s-]/g, '')}">
                                    ${item.category}
                                </div>
                                <div class="product-stock">
                                    Stock: ${item.stock}
                                    ${item.stock <= 0 ? '<div class="out-of-stock-badge">Out of Stock</div>' : ''}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        // Add item to order (using Firebase data)
        function addToOrder(itemId) {
            console.log("Adding to order, item ID:", itemId);
            
            const item = firebaseMenuItems.find(i => i.id === itemId);
            console.log("Found item:", item);
            
            if (!item) {
                console.error("Item not found in firebaseMenuItems:", itemId);
                return;
            }
            
            if (item.stock <= 0) {
                alert('Item out of stock!');
                return;
            }
            
            const existingItem = currentOrder.find(i => i.id === itemId);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                currentOrder.push({
                    id: item.id,
                    name: item.name,
                    price: item.price,
                    quantity: 1,
                    image: item.image
                });
            }
            
            updateOrderDisplay();
        }

        // Update order display
        function updateOrderDisplay() {
            const orderItems = document.getElementById('orderItems');
            const subtotalEl = document.getElementById('subtotal');
            const taxEl = document.getElementById('tax');
            const grandTotalEl = document.getElementById('grandTotal');
            
            if (currentOrder.length === 0) {
                orderItems.innerHTML = `
                    <div style="text-align: center; color: #666; padding: 40px 20px;">
                        <i class="fas fa-shopping-cart" style="font-size: 3em; margin-bottom: 15px; opacity: 0.3;"></i>
                        <p>No items in order</p>
                    </div>
                `;
                subtotalEl.textContent = '₱0.00';
                taxEl.textContent = '₱0.00';
                grandTotalEl.textContent = '₱0.00';
                return;
            }
            
            let itemsHTML = '';
            let subtotal = 0;
            
            currentOrder.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                itemsHTML += `
                    <div class="order-item">
                        <div class="item-info">
                            <div class="item-name">${item.name}</div>
                            <div class="item-price">₱${item.price.toFixed(2)} each</div>
                        </div>
                        <div class="item-quantity">
                            <button class="quantity-btn" onclick="updateQuantity('${item.id}', -1)">-</button>
                            <span class="quantity-display">${item.quantity}</span>
                            <button class="quantity-btn" onclick="updateQuantity('${item.id}', 1)">+</button>
                            <button class="quantity-btn remove-btn" onclick="removeItem('${item.id}')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            const tax = subtotal * 0.12;
            const grandTotal = subtotal + tax;
            
            orderItems.innerHTML = itemsHTML;
            subtotalEl.textContent = `₱${subtotal.toFixed(2)}`;
            taxEl.textContent = `₱${tax.toFixed(2)}`;
            grandTotalEl.textContent = `₱${grandTotal.toFixed(2)}`;
        }

        // Update quantity
        function updateQuantity(itemId, change) {
            const item = currentOrder.find(i => i.id === itemId);
            
            if (item) {
                item.quantity += change;
                
                if (item.quantity <= 0) {
                    currentOrder = currentOrder.filter(i => i.id !== itemId);
                }
            }
            
            updateOrderDisplay();
        }

        // Remove item from order
        function removeItem(itemId) {
            currentOrder = currentOrder.filter(i => i.id !== itemId);
            updateOrderDisplay();
        }

        // Enhanced category filtering
        function filterProducts(category) {
            // Update active category button
            document.querySelectorAll('.category-btn-large').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Find the clicked button and activate it
            const clickedBtn = event.target.closest('.category-btn-large');
            if (clickedBtn) {
                clickedBtn.classList.add('active');
            }
            
            // Show selected category section
            document.querySelectorAll('.category-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Convert category to proper ID format (handle "Milk Tea" -> "milk-tea")
            const categoryId = category.toLowerCase().replace(' ', '-');
            const targetSection = document.getElementById(`category-${categoryId}`);
            if (targetSection) {
                targetSection.classList.add('active');
            }
            
            // Update URL for refresh persistence
            history.replaceState(null, null, `#${category}`);
        }

        // Clear current order
        function clearOrder() {
            if (currentOrder.length === 0) return;
            
            if (confirm('Clear current order?')) {
                currentOrder = [];
                updateOrderDisplay();
            }
        }

        // Process order with payment method selection
        function processOrder() {
            if (currentOrder.length === 0) {
                alert('Please add items to the order first.');
                return;
            }
            
            // Show payment method modal
            showPaymentModal();
        }

        // Show payment method selection modal
        function showPaymentModal() {
            document.getElementById('paymentModal').style.display = 'flex';
        }

        // Close payment method modal
        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }

        // Select payment method
        function selectPaymentMethod(method) {
            closePaymentModal();
            
            switch(method) {
                case 'cash':
                    showCashModal();
                    break;
                case 'credit_card':
                    processCreditCardPayment();
                    break;
                case 'qr':
                    processQRPayment();
                    break;
            }
        }

        // Show cash payment modal
        function showCashModal() {
            const subtotal = currentOrder.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const tax = subtotal * 0.12;
            const grandTotal = subtotal + tax;
            
            document.getElementById('totalAmountDisplay').textContent = `₱${grandTotal.toFixed(2)}`;
            document.getElementById('cashAmount').value = '';
            document.getElementById('changeAmount').textContent = '₱0.00';
            document.getElementById('confirmCashPayment').disabled = true;
            
            // Reset input classes
            document.getElementById('cashAmount').classList.remove('valid', 'invalid');
            
            document.getElementById('cashModal').style.display = 'flex';
            
            // Focus on cash input
            setTimeout(() => {
                document.getElementById('cashAmount').focus();
            }, 100);
        }

        // Close cash modal
        function closeCashModal() {
            document.getElementById('cashModal').style.display = 'none';
            showPaymentModal(); // Return to payment method selection
        }

        // Calculate change for cash payment
        function calculateChange() {
            const cashAmount = parseFloat(document.getElementById('cashAmount').value) || 0;
            const subtotal = currentOrder.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const tax = subtotal * 0.12;
            const grandTotal = subtotal + tax;
            
            const change = cashAmount - grandTotal;
            const changeElement = document.getElementById('changeAmount');
            const confirmButton = document.getElementById('confirmCashPayment');
            const cashInput = document.getElementById('cashAmount');
            
            if (change >= 0) {
                changeElement.textContent = `₱${change.toFixed(2)}`;
                changeElement.style.color = 'var(--success-color)';
                confirmButton.disabled = false;
                cashInput.classList.remove('invalid');
                cashInput.classList.add('valid');
            } else {
                changeElement.textContent = `-₱${Math.abs(change).toFixed(2)}`;
                changeElement.style.color = 'var(--danger-color)';
                confirmButton.disabled = true;
                cashInput.classList.remove('valid');
                cashInput.classList.add('invalid');
            }
        }

        // Confirm cash payment
        async function confirmCashPayment() {
            const cashAmount = parseFloat(document.getElementById('cashAmount').value);
            const subtotal = currentOrder.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const tax = subtotal * 0.12;
            const grandTotal = subtotal + tax;
            const change = cashAmount - grandTotal;
            
            closeCashModal();
            showProcessingModal();
            
            try {
                // Update stock in Firebase
                const batch = db.batch();
                
                for (const orderItem of currentOrder) {
                    const itemRef = db.collection('menu_items').doc(orderItem.id);
                    const item = firebaseMenuItems.find(i => i.id === orderItem.id);
                    
                    if (item) {
                        const newStock = item.stock - orderItem.quantity;
                        batch.update(itemRef, { stock: newStock });
                    }
                }
                
                await batch.commit();
                
                // Add transaction to Firebase with payment method
                await db.collection('transactions').add({
                    timestamp: new Date(),
                    cashier: '<?php echo $current_user['name']; ?>',
                    items: currentOrder,
                    subtotal: subtotal,
                    tax: tax,
                    total: grandTotal,
                    payment_method: 'cash',
                    cash_received: cashAmount,
                    change_given: change,
                    voided: false
                });
                
                // Refresh menu items to get updated stock
                await fetchMenuItems();
                
                // Show success modal
                showSuccessModal('Cash payment processed successfully!', grandTotal, change);
                
            } catch (error) {
                console.error('Error processing cash payment:', error);
                hideProcessingModal();
                alert('Error processing payment. Please try again.');
            }
        }

        // Process credit card payment
        async function processCreditCardPayment() {
            showProcessingModal();
            
            try {
                // Simulate credit card processing delay
                await new Promise(resolve => setTimeout(resolve, 3000));
                
                const subtotal = currentOrder.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                const tax = subtotal * 0.12;
                const grandTotal = subtotal + tax;
                
                // Update stock in Firebase
                const batch = db.batch();
                
                for (const orderItem of currentOrder) {
                    const itemRef = db.collection('menu_items').doc(orderItem.id);
                    const item = firebaseMenuItems.find(i => i.id === orderItem.id);
                    
                    if (item) {
                        const newStock = item.stock - orderItem.quantity;
                        batch.update(itemRef, { stock: newStock });
                    }
                }
                
                await batch.commit();
                
                // Add transaction to Firebase with payment method
                await db.collection('transactions').add({
                    timestamp: new Date(),
                    cashier: '<?php echo $current_user['name']; ?>',
                    items: currentOrder,
                    subtotal: subtotal,
                    tax: tax,
                    total: grandTotal,
                    payment_method: 'credit_card',
                    voided: false
                });
                
                // Refresh menu items to get updated stock
                await fetchMenuItems();
                
                // Show success modal
                showSuccessModal('Credit card payment processed successfully!', grandTotal);
                
            } catch (error) {
                console.error('Error processing credit card payment:', error);
                hideProcessingModal();
                alert('Error processing credit card payment. Please try again.');
            }
        }

        // Process QR payment
        async function processQRPayment() {
            showProcessingModal();
            
            try {
                // Simulate QR payment processing delay
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                const subtotal = currentOrder.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                const tax = subtotal * 0.12;
                const grandTotal = subtotal + tax;
                
                // Update stock in Firebase
                const batch = db.batch();
                
                for (const orderItem of currentOrder) {
                    const itemRef = db.collection('menu_items').doc(orderItem.id);
                    const item = firebaseMenuItems.find(i => i.id === orderItem.id);
                    
                    if (item) {
                        const newStock = item.stock - orderItem.quantity;
                        batch.update(itemRef, { stock: newStock });
                    }
                }
                
                await batch.commit();
                
                // Add transaction to Firebase with payment method
                await db.collection('transactions').add({
                    timestamp: new Date(),
                    cashier: '<?php echo $current_user['name']; ?>',
                    items: currentOrder,
                    subtotal: subtotal,
                    tax: tax,
                    total: grandTotal,
                    payment_method: 'qr',
                    voided: false
                });
                
                // Refresh menu items to get updated stock
                await fetchMenuItems();
                
                // Show success modal
                showSuccessModal('QR payment processed successfully!', grandTotal);
                
            } catch (error) {
                console.error('Error processing QR payment:', error);
                hideProcessingModal();
                alert('Error processing QR payment. Please try again.');
            }
        }

        // Show processing modal
        function showProcessingModal() {
            document.getElementById('processingModal').style.display = 'flex';
        }

        // Hide processing modal
        function hideProcessingModal() {
            document.getElementById('processingModal').style.display = 'none';
        }

        // Show success modal
        function showSuccessModal(message, total, change = null) {
            hideProcessingModal();
            
            let successMessage = message;
            if (change !== null) {
                successMessage += `<br>Change: ₱${change.toFixed(2)}`;
            }
            
            document.getElementById('successMessage').innerHTML = successMessage;
            document.getElementById('successModal').style.display = 'flex';
        }

        // Close success modal and reset
        function closeSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
            
            // Clear current order
            currentOrder = [];
            updateOrderDisplay();
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const paymentModal = document.getElementById('paymentModal');
            const cashModal = document.getElementById('cashModal');
            const successModal = document.getElementById('successModal');
            
            if (event.target === paymentModal) {
                closePaymentModal();
            }
            if (event.target === cashModal) {
                closeCashModal();
            }
            if (event.target === successModal) {
                closeSuccessModal();
            }
        });

        // Function to handle logout with status update
        async function handleLogout() {
            const staffId = '<?php echo isset($_SESSION["staff_id"]) ? $_SESSION["staff_id"] : ""; ?>';
            const isDemoAccount = <?php echo isset($_SESSION['is_demo_account']) && $_SESSION['is_demo_account'] ? 'true' : 'false'; ?>;
            
            // Update status for Firestore staff accounts
            if (!isDemoAccount && staffId) {
                try {
                    await db.collection('staff').doc(staffId).update({
                        isActive: false,
                        lastLogout: new Date(),
                        lastActivity: new Date()
                    });
                } catch (error) {
                    console.error('Error updating staff status on logout:', error);
                }
            }
            
            // Redirect to logout
            window.location.href = 'admin.php?action=logout';
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Cashier dashboard loaded");
            
            // Hide error state initially
            document.getElementById('errorState').style.display = 'none';
            
            // Hide categories and products initially
            document.getElementById('categoriesNav').style.display = 'none';
            document.getElementById('productsContainer').style.display = 'none';
            
            fetchMenuItems();
            updateOrderDisplay();
            
            // Add keyboard event listener for cash amount input
            document.getElementById('cashAmount')?.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !this.classList.contains('invalid')) {
                    confirmCashPayment();
                }
            });
        });
    </script>
</body>
</html>