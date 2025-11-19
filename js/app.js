// Main Application JavaScript for Brew & Bean Café
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all Materialize components
    initMaterializeComponents();
    
    // Initialize application functionality
    initApp();
});

/**
 * Initialize all Materialize CSS components
 */
function initMaterializeComponents() {
    // Sidenav for mobile navigation
    const sidenavs = document.querySelectorAll('.sidenav');
    M.Sidenav.init(sidenavs);
    
    // Parallax for hero sections
    const parallax = document.querySelectorAll('.parallax');
    M.Parallax.init(parallax);
    
    // Modals
    const modals = document.querySelectorAll('.modal');
    M.Modal.init(modals);
    
    // Select dropdowns
    const selects = document.querySelectorAll('select');
    M.FormSelect.init(selects);
    
    // Tooltips
    const tooltips = document.querySelectorAll('.tooltipped');
    M.Tooltip.init(tooltips);
    
    // Floating Action Button
    const fab = document.querySelectorAll('.fixed-action-btn');
    M.FloatingActionButton.init(fab);
    
    // Collapsible elements
    const collapsibles = document.querySelectorAll('.collapsible');
    M.Collapsible.init(collapsibles);
    
    // Material Box for images
    const materialboxes = document.querySelectorAll('.materialboxed');
    M.Materialbox.init(materialboxes);
    
    console.log('Materialize components initialized');
}

/**
 * Initialize application-specific functionality
 */
function initApp() {
    initCartFunctionality();
    initMenuInteractions();
    initFormValidations();
    initOrderTracking();
    initAdminFunctions();
    initLoadingStates();
}

/**
 * Shopping Cart Functionality
 */
function initCartFunctionality() {
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', handleAddToCart);
    });
    
    // Quantity updates in cart
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', handleQuantityUpdate);
    });
    
    // Remove item from cart
    const removeButtons = document.querySelectorAll('.remove-item');
    removeButtons.forEach(button => {
        button.addEventListener('click', handleRemoveItem);
    });
    
    // Clear cart
    const clearCartBtn = document.getElementById('clear-cart');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', handleClearCart);
    }
}

/**
 * Handle adding items to cart
 */
async function handleAddToCart(event) {
    event.preventDefault();
    
    const button = event.currentTarget;
    const itemId = button.getAttribute('data-id');
    const itemName = button.getAttribute('data-name');
    const itemPrice = button.getAttribute('data-price');
    
    // Show loading state
    button.classList.add('disabled');
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="material-icons">hourglass_empty</i>';
    
    try {
        const response = await fetch('add-to-cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `item_id=${encodeURIComponent(itemId)}&name=${encodeURIComponent(itemName)}&price=${encodeURIComponent(itemPrice)}`
        });
        
        if (response.ok) {
            // Show success toast
            showToast(`${itemName} added to cart!`, 'green');
            
            // Update cart count in navigation
            updateCartCount();
            
            // Add animation to cart icon
            animateCartIcon();
            
        } else {
            throw new Error('Failed to add item to cart');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showToast('Failed to add item to cart. Please try again.', 'red');
    } finally {
        // Restore button state
        setTimeout(() => {
            button.classList.remove('disabled');
            button.innerHTML = originalHTML;
        }, 1000);
    }
}

/**
 * Handle quantity updates in cart
 */
function handleQuantityUpdate(event) {
    const input = event.target;
    const index = input.getAttribute('data-index');
    const quantity = input.value;
    
    if (quantity > 0 && quantity <= 99) {
        window.location.href = `update-cart.php?index=${index}&quantity=${quantity}`;
    } else {
        showToast('Please enter a valid quantity (1-99)', 'orange');
        input.value = input.getAttribute('data-original-value');
    }
}

/**
 * Handle item removal from cart
 */
function handleRemoveItem(event) {
    event.preventDefault();
    
    const button = event.currentTarget;
    const itemName = button.getAttribute('data-name') || 'Item';
    
    // Show confirmation dialog
    if (confirm(`Are you sure you want to remove ${itemName} from your cart?`)) {
        window.location.href = button.href;
    }
}

/**
 * Handle clear cart
 */
function handleClearCart(event) {
    event.preventDefault();
    
    if (confirm('Are you sure you want to clear your entire cart?')) {
        window.location.href = 'clear-cart.php';
    }
}

/**
 * Update cart count in navigation
 */
function updateCartCount() {
    const cartBadges = document.querySelectorAll('.cart-badge, .cart-count');
    
    // This would typically fetch the actual count from the server
    // For now, we'll increment the existing count
    cartBadges.forEach(badge => {
        let currentCount = parseInt(badge.textContent) || 0;
        badge.textContent = currentCount + 1;
        badge.style.display = 'inline-block';
    });
}

/**
 * Animate cart icon when item is added
 */
function animateCartIcon() {
    const cartIcons = document.querySelectorAll('.cart-icon');
    
    cartIcons.forEach(icon => {
        icon.style.transform = 'scale(1.2)';
        icon.style.color = '#4caf50';
        
        setTimeout(() => {
            icon.style.transform = 'scale(1)';
            icon.style.color = '';
        }, 500);
    });
}

/**
 * Menu Interactions and Filtering
 */
function initMenuInteractions() {
    // Category filtering
    const categoryFilters = document.querySelectorAll('.category-filter');
    categoryFilters.forEach(filter => {
        filter.addEventListener('click', handleCategoryFilter);
    });
    
    // Search functionality
    const searchInput = document.getElementById('menu-search');
    if (searchInput) {
        searchInput.addEventListener('input', handleMenuSearch);
    }
    
    // Sort functionality
    const sortSelect = document.getElementById('sort-menu');
    if (sortSelect) {
        sortSelect.addEventListener('change', handleMenuSort);
    }
    
    // Quick view modals
    const quickViewButtons = document.querySelectorAll('.quick-view-btn');
    quickViewButtons.forEach(button => {
        button.addEventListener('click', handleQuickView);
    });
}

/**
 * Handle category filtering
 */
function handleCategoryFilter(event) {
    event.preventDefault();
    
    const category = event.currentTarget.getAttribute('data-category');
    const menuItems = document.querySelectorAll('.menu-item');
    
    // Update active filter
    document.querySelectorAll('.category-filter').forEach(btn => {
        btn.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    // Show/hide items based on category
    menuItems.forEach(item => {
        const itemCategory = item.getAttribute('data-category');
        
        if (category === 'all' || itemCategory === category) {
            item.style.display = 'block';
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, 50);
        } else {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            setTimeout(() => {
                item.style.display = 'none';
            }, 300);
        }
    });
    
    showToast(`Showing ${category === 'all' ? 'all' : category} items`, 'brown');
}

/**
 * Handle menu search
 */
function handleMenuSearch(event) {
    const searchTerm = event.target.value.toLowerCase();
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        const itemName = item.getAttribute('data-name').toLowerCase();
        const itemDescription = item.getAttribute('data-description').toLowerCase();
        
        if (itemName.includes(searchTerm) || itemDescription.includes(searchTerm)) {
            item.style.display = 'block';
            setTimeout(() => {
                item.style.opacity = '1';
            }, 50);
        } else {
            item.style.opacity = '0';
            setTimeout(() => {
                item.style.display = 'none';
            }, 300);
        }
    });
}

/**
 * Handle menu sorting
 */
function handleMenuSort(event) {
    const sortBy = event.target.value;
    const menuContainer = document.querySelector('.menu-items-container');
    const menuItems = Array.from(document.querySelectorAll('.menu-item'));
    
    menuItems.sort((a, b) => {
        switch (sortBy) {
            case 'price-low':
                return parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
            case 'price-high':
                return parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price'));
            case 'name':
                return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
            default:
                return 0;
        }
    });
    
    // Reappend sorted items
    menuItems.forEach(item => {
        menuContainer.appendChild(item);
    });
    
    // Add animation
    menuItems.forEach((item, index) => {
        setTimeout(() => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, 50);
        }, index * 50);
    });
}

/**
 * Handle quick view modal
 */
function handleQuickView(event) {
    event.preventDefault();
    
    const button = event.currentTarget;
    const itemId = button.getAttribute('data-id');
    
    // In a real application, you would fetch item details from the server
    // For now, we'll use the data attributes
    const itemName = button.getAttribute('data-name');
    const itemPrice = button.getAttribute('data-price');
    const itemDescription = button.getAttribute('data-description');
    const itemImage = button.getAttribute('data-image');
    
    // Populate quick view modal
    document.getElementById('quick-view-name').textContent = itemName;
    document.getElementById('quick-view-price').textContent = `$${parseFloat(itemPrice).toFixed(2)}`;
    document.getElementById('quick-view-description').textContent = itemDescription;
    document.getElementById('quick-view-image').src = itemImage;
    document.getElementById('quick-view-image').alt = itemName;
    
    // Update add to cart button in modal
    const modalAddBtn = document.getElementById('quick-view-add-to-cart');
    modalAddBtn.setAttribute('data-id', itemId);
    modalAddBtn.setAttribute('data-name', itemName);
    modalAddBtn.setAttribute('data-price', itemPrice);
    
    // Open modal
    const modal = M.Modal.getInstance(document.getElementById('quick-view-modal'));
    modal.open();
}

/**
 * Form Validations
 */
function initFormValidations() {
    // Checkout form validation
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', validateCheckoutForm);
    }
    
    // Contact form validation
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', validateContactForm);
    }
    
    // Real-time form validation
    const formInputs = document.querySelectorAll('input[required], textarea[required]');
    formInputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearFieldError);
    });
}

/**
 * Validate checkout form
 */
function validateCheckoutForm(event) {
    let isValid = true;
    const form = event.target;
    
    // Validate required fields
    const requiredFields = form.querySelectorAll('input[required], textarea[required]');
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'This field is required');
            isValid = false;
        }
    });
    
    // Validate email
    const emailField = form.querySelector('input[type="email"]');
    if (emailField && !isValidEmail(emailField.value)) {
        showFieldError(emailField, 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate phone
    const phoneField = form.querySelector('input[type="tel"]');
    if (phoneField && !isValidPhone(phoneField.value)) {
        showFieldError(phoneField, 'Please enter a valid phone number');
        isValid = false;
    }
    
    if (!isValid) {
        event.preventDefault();
        showToast('Please fix the errors in the form', 'red');
    } else {
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.classList.add('disabled');
            submitBtn.innerHTML = 'Processing... <i class="material-icons right">hourglass_empty</i>';
        }
    }
    
    return isValid;
}

/**
 * Validate contact form
 */
function validateContactForm(event) {
    // Similar validation as checkout form
    return validateCheckoutForm(event);
}

/**
 * Validate individual field
 */
function validateField(event) {
    const field = event.target;
    const value = field.value.trim();
    
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required');
        return false;
    }
    
    if (field.type === 'email' && value && !isValidEmail(value)) {
        showFieldError(field, 'Please enter a valid email address');
        return false;
    }
    
    if (field.type === 'tel' && value && !isValidPhone(value)) {
        showFieldError(field, 'Please enter a valid phone number');
        return false;
    }
    
    clearFieldError(field);
    return true;
}

/**
 * Show field error
 */
function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('invalid');
    
    const errorElement = document.createElement('span');
    errorElement.className = 'helper-text red-text';
    errorElement.setAttribute('data-error', message);
    errorElement.textContent = message;
    
    field.parentNode.appendChild(errorElement);
}

/**
 * Clear field error
 */
function clearFieldError(field) {
    field.classList.remove('invalid');
    field.classList.add('valid');
    
    const existingError = field.parentNode.querySelector('.helper-text');
    if (existingError) {
        existingError.remove();
    }
}

/**
 * Utility function to validate email
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Utility function to validate phone number
 */
function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

/**
 * Order Tracking Functionality
 */
function initOrderTracking() {
    const trackOrderBtn = document.getElementById('track-order-btn');
    if (trackOrderBtn) {
        trackOrderBtn.addEventListener('click', handleOrderTracking);
    }
    
    // Auto-update order status if on order tracking page
    if (document.getElementById('order-status')) {
        startOrderStatusPolling();
    }
}

/**
 * Handle order tracking
 */
function handleOrderTracking(event) {
    event.preventDefault();
    
    const orderId = document.getElementById('track-order-id').value.trim();
    
    if (!orderId) {
        showToast('Please enter an order ID', 'orange');
        return;
    }
    
    trackOrderStatus(orderId);
}

/**
 * Track order status
 */
async function trackOrderStatus(orderId) {
    try {
        // Show loading
        const statusElement = document.getElementById('order-status');
        if (statusElement) {
            statusElement.innerHTML = '<div class="progress"><div class="indeterminate"></div></div>';
        }
        
        // In a real application, you would make an API call to fetch order status
        // For demo purposes, we'll simulate an API call
        const response = await simulateOrderStatusAPI(orderId);
        
        if (response.success) {
            updateOrderStatusDisplay(response.order);
        } else {
            showToast('Order not found. Please check your order ID.', 'red');
        }
    } catch (error) {
        console.error('Error tracking order:', error);
        showToast('Error tracking order. Please try again.', 'red');
    }
}

/**
 * Simulate order status API call
 */
function simulateOrderStatusAPI(orderId) {
    return new Promise((resolve) => {
        setTimeout(() => {
            // Mock response - in real app, this would come from your backend
            const mockOrders = {
                'ORD123': { status: 'preparing', estimatedTime: '15-20 minutes' },
                'ORD456': { status: 'ready', estimatedTime: 'Ready for pickup' },
                'ORD789': { status: 'delivered', estimatedTime: 'Delivered' }
            };
            
            if (mockOrders[orderId]) {
                resolve({
                    success: true,
                    order: {
                        id: orderId,
                        ...mockOrders[orderId]
                    }
                });
            } else {
                resolve({ success: false });
            }
        }, 1500);
    });
}

/**
 * Update order status display
 */
function updateOrderStatusDisplay(order) {
    const statusElement = document.getElementById('order-status');
    if (!statusElement) return;
    
    const statusColors = {
        'pending': 'orange',
        'preparing': 'blue',
        'ready': 'green',
        'delivered': 'grey'
    };
    
    const statusText = {
        'pending': 'Order Received',
        'preparing': 'Being Prepared',
        'ready': 'Ready for Pickup',
        'delivered': 'Delivered'
    };
    
    statusElement.innerHTML = `
        <div class="card-panel ${statusColors[order.status]} lighten-5">
            <h5>Order #${order.id}</h5>
            <p><strong>Status:</strong> ${statusText[order.status]}</p>
            <p><strong>Estimated Time:</strong> ${order.estimatedTime}</p>
            <div class="progress">
                <div class="determinate ${statusColors[order.status]}" style="width: ${
                    order.status === 'pending' ? 25 : 
                    order.status === 'preparing' ? 50 :
                    order.status === 'ready' ? 75 : 100
                }%"></div>
            </div>
        </div>
    `;
}

/**
 * Start polling for order status updates
 */
function startOrderStatusPolling() {
    const orderId = document.getElementById('order-status').getAttribute('data-order-id');
    if (orderId) {
        setInterval(() => {
            trackOrderStatus(orderId);
        }, 30000); // Poll every 30 seconds
    }
}

/**
 * Admin Functions
 */
function initAdminFunctions() {
    // Only initialize if on admin page
    if (!document.querySelector('.admin-panel')) return;
    
    initAdminItemManagement();
    initAdminOrderManagement();
    initAdminStatistics();
}

/**
 * Initialize admin item management
 */
function initAdminItemManagement() {
    // Edit item modal
    const editItemButtons = document.querySelectorAll('.edit-item-btn');
    editItemButtons.forEach(button => {
        button.addEventListener('click', handleEditItem);
    });
    
    // Delete item confirmation
    const deleteItemButtons = document.querySelectorAll('.delete-item-btn');
    deleteItemButtons.forEach(button => {
        button.addEventListener('click', handleDeleteItem);
    });
    
    // Image preview for new items
    const imageInput = document.getElementById('item-image');
    if (imageInput) {
        imageInput.addEventListener('change', handleImagePreview);
    }
}

/**
 * Handle edit item
 */
function handleEditItem(event) {
    event.preventDefault();
    
    const button = event.currentTarget;
    const itemId = button.getAttribute('data-id');
    
    // Fetch item details and populate edit form
    // This would typically make an API call
    console.log('Editing item:', itemId);
}

/**
 * Handle delete item
 */
function handleDeleteItem(event) {
    event.preventDefault();
    
    const button = event.currentTarget;
    const itemId = button.getAttribute('data-id');
    const itemName = button.getAttribute('data-name');
    
    if (confirm(`Are you sure you want to delete "${itemName}"? This action cannot be undone.`)) {
        // Proceed with deletion
        window.location.href = button.href;
    }
}

/**
 * Handle image preview
 */
function handleImagePreview(event) {
    const input = event.target;
    const preview = document.getElementById('image-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="responsive-img" style="max-height: 200px;">`;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Initialize admin order management
 */
function initAdminOrderManagement() {
    // Update order status
    const statusSelects = document.querySelectorAll('.order-status-select');
    statusSelects.forEach(select => {
        select.addEventListener('change', handleOrderStatusUpdate);
    });
    
    // View order details
    const viewOrderButtons = document.querySelectorAll('.view-order-btn');
    viewOrderButtons.forEach(button => {
        button.addEventListener('click', handleViewOrder);
    });
}

/**
 * Handle order status update
 */
function handleOrderStatusUpdate(event) {
    const select = event.target;
    const orderId = select.getAttribute('data-order-id');
    const newStatus = select.value;
    
    // Show loading
    const originalValue = select.value;
    select.disabled = true;
    
    // Simulate API call to update status
    setTimeout(() => {
        select.disabled = false;
        showToast(`Order #${orderId} status updated to ${newStatus}`, 'green');
        
        // Update badge color based on status
        const badge = select.closest('tr').querySelector('.status-badge');
        if (badge) {
            const statusColors = {
                'pending': 'orange',
                'preparing': 'blue',
                'ready': 'green',
                'delivered': 'grey'
            };
            badge.className = `status-badge ${statusColors[newStatus]}`;
            badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
        }
    }, 1000);
}

/**
 * Handle view order details
 */
function handleViewOrder(event) {
    event.preventDefault();
    
    const button = event.currentTarget;
    const orderId = button.getAttribute('data-order-id');
    
    // Fetch and display order details in modal
    // This would typically make an API call
    console.log('Viewing order:', orderId);
}

/**
 * Initialize admin statistics
 */
function initAdminStatistics() {
    // Refresh statistics
    const refreshStatsBtn = document.getElementById('refresh-stats');
    if (refreshStatsBtn) {
        refreshStatsBtn.addEventListener('click', refreshStatistics);
    }
    
    // Load initial statistics
    loadStatistics();
}

/**
 * Load admin statistics
 */
async function loadStatistics() {
    try {
        // Simulate API call to fetch statistics
        const stats = await simulateStatisticsAPI();
        updateStatisticsDisplay(stats);
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

/**
 * Simulate statistics API
 */
function simulateStatisticsAPI() {
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve({
                totalOrders: 142,
                pendingOrders: 8,
                revenue: 2847.50,
                popularItem: 'Cappuccino'
            });
        }, 1000);
    });
}

/**
 * Update statistics display
 */
function updateStatisticsDisplay(stats) {
    // Animate counting up
    animateValue('total-orders', 0, stats.totalOrders, 1000);
    animateValue('pending-orders', 0, stats.pendingOrders, 1000);
    animateValue('total-revenue', 0, stats.revenue, 1000, true);
    
    document.getElementById('popular-item').textContent = stats.popularItem;
}

/**
 * Refresh statistics
 */
function refreshStatistics() {
    const btn = document.getElementById('refresh-stats');
    btn.classList.add('disabled');
    btn.innerHTML = '<i class="material-icons">refresh</i> Refreshing...';
    
    setTimeout(() => {
        loadStatistics();
        btn.classList.remove('disabled');
        btn.innerHTML = '<i class="material-icons">refresh</i> Refresh Stats';
        showToast('Statistics updated', 'green');
    }, 1500);
}

/**
 * Loading States
 */
function initLoadingStates() {
    // Add loading states to all buttons with async actions
    const asyncButtons = document.querySelectorAll('button[type="submit"], .btn[href="#"]');
    asyncButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (!this.classList.contains('disabled')) {
                this.classList.add('disabled');
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="material-icons left">hourglass_empty</i> Loading...';
                
                // Revert after 5 seconds (safety net)
                setTimeout(() => {
                    this.classList.remove('disabled');
                    this.innerHTML = originalText;
                }, 5000);
            }
        });
    });
}

/**
 * Utility Functions
 */

/**
 * Show Materialize toast notification
 */
function showToast(message, color = '') {
    const className = color ? ` ${color}` : '';
    M.toast({html: message, classes: `rounded${className}`});
}

/**
 * Animate value counting up
 */
function animateValue(elementId, start, end, duration, isCurrency = false) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const range = end - start;
    const increment = end > start ? 1 : -1;
    const stepTime = Math.abs(Math.floor(duration / range));
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        element.textContent = isCurrency ? `$${current.toFixed(2)}` : current;
        
        if (current === end) {
            clearInterval(timer);
        }
    }, stepTime);
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

/**
 * Debounce function for performance
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Throttle function for performance
 */
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Make functions available globally
window.app = {
    showToast,
    formatCurrency,
    debounce,
    throttle
};