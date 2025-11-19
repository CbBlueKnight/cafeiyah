<?php
session_start();

// Check if user is logged in and is manager or owner
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['manager', 'owner'])) {
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
    <title>Cafe Management - Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/manager.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>
</head>
<body>
    <!-- Mobile Menu Toggle Button -->
    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
        <i class="fas fa-bars"></i> Show Menu
    </button>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <i class="fas fa-coffee"></i>
                <h1>Cafe Management</h1>
            </div>
            
            <div class="user-info">
                <h2><?php echo $current_user['name']; ?></h2>
                <div class="role-badge <?php echo $current_user['role']; ?>-badge">
                    <?php echo ucfirst($current_user['role']); ?>
                </div>
                <div class="staff-status-section">
                    <span class="online-indicator online"></span>
                    <span class="status-badge status-active">Active</span>
                </div>
                <p class="last-login">Logged in: <?php echo isset($_SESSION['login_time']) ? $_SESSION['login_time'] : date('Y-m-d H:i:s'); ?></p>
            </div>

            <nav style="margin-top: 30px;">
                <ul style="list-style: none;">
                    <li style="margin-bottom: 10px;">
                        <a href="#dashboard" onclick="showSection('dashboard')" style="display: block; padding: 15px; background: #f8f9fa; border-radius: 10px; text-decoration: none; color: var(--text-color); font-weight: 500; transition: all 0.3s;">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="#menu" onclick="showSection('menu')" style="display: block; padding: 15px; background: #f8f9fa; border-radius: 10px; text-decoration: none; color: var(--text-color); font-weight: 500; transition: all 0.3s;">
                            <i class="fas fa-utensils"></i> Menu Management
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="#staff" onclick="showSection('staff')" style="display: block; padding: 15px; background: #f8f9fa; border-radius: 10px; text-decoration: none; color: var(--text-color); font-weight: 500; transition: all 0.3s;">
                            <i class="fas fa-users"></i> Staff Management
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="#inventory" onclick="showSection('inventory')" style="display: block; padding: 15px; background: #f8f9fa; border-radius: 10px; text-decoration: none; color: var(--text-color); font-weight: 500; transition: all 0.3s;">
                            <i class="fas fa-boxes"></i> Inventory
                        </a>
                    </li>
                </ul>
            </nav>

            <a href="admin.php?action=logout" onclick="handleLogout()" style="display: block; text-decoration: none; margin-top: 30px;">
                <button class="logout-btn" style="background: var(--danger-color); width: 100%;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </a>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Dashboard Section -->
            <div id="dashboard-section" class="content-section">
                <h2 style="color: var(--dark-color); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-tachometer-alt"></i> Manager Dashboard
                </h2>
                
                <div class="stats-grid">
                    <div class="stat-card clickable">
                        <i class="fas fa-shopping-cart"></i>
                        <div class="stat-number" id="totalSales">₱0.00</div>
                        <div class="stat-label">Total Sales Today</div>
                    </div>
                    <div class="stat-card clickable">
                        <i class="fas fa-utensils"></i>
                        <div class="stat-number" id="totalOrders">0</div>
                        <div class="stat-label">Orders Today</div>
                    </div>
                    <div class="stat-card clickable">
                        <i class="fas fa-users"></i>
                        <div class="stat-number" id="activeStaff">0</div>
                        <div class="stat-label">Active Cashiers</div>
                    </div>
                    <div class="stat-card clickable">
                        <i class="fas fa-box"></i>
                        <div class="stat-number" id="lowStock">0</div>
                        <div class="stat-label">Low Stock Items</div>
                    </div>
                </div>
            </div>

            <!-- Menu Management Section -->
            <div id="menu-section" class="content-section" style="display: none;">
                <h2 style="color: var(--dark-color); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-utensils"></i> Menu Management
                </h2>
                
                <div class="access-section">
                    <h3><i class="fas fa-plus-circle"></i> Add New Menu Item</h3>
                    <form id="addMenuItemForm">
                        <div class="form-group">
                            <label>Item Name</label>
                            <input type="text" id="item_name" name="item_name" placeholder="Enter item name" required>
                        </div>
                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" id="item_price" name="item_price" step="0.01" placeholder="Enter price" required>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select id="item_category" name="item_category" required>
                                <option value="Espresso">Espresso</option>
                                <option value="Frappe">Frappe</option>
                                <option value="Milk Tea">Milk Tea</option>
                                <option value="Food">Food</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea id="item_description" name="item_description" placeholder="Enter item description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Emoji</label>
                            <select id="item_emoji" name="item_emoji" required>
                                <option value="☕">☕ Espresso</option>
                                <option value="🥤">🥤 Frappe</option>
                                <option value="🧋">🧋 Milk Tea</option>
                                <option value="🍛">🍛 Food</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Stock Quantity</label>
                            <input type="number" id="item_stock" name="item_stock" placeholder="Enter stock quantity" required>
                        </div>
                        <button type="submit">Add Menu Item to Firestore</button>
                    </form>
                </div>
                <!-- Removed the "Current Menu Items" section -->
            </div>

            <!-- Staff Management Section -->
            <div id="staff-section" class="content-section" style="display: none;">
                <h2 style="color: var(--dark-color); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-users"></i> Staff Management
                </h2>
                
                <div class="access-section">
                    <h3><i class="fas fa-plus-circle"></i> Add New Staff Member</h3>
                    <form id="addStaffForm">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" id="staff_name" name="staff_name" placeholder="Enter full name" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" id="staff_phone" name="staff_phone" placeholder="Enter phone number" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" id="staff_email" name="staff_email" placeholder="Enter email address" required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" id="staff_username" name="staff_username" placeholder="Enter username" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <div class="password-input-container">
                                <input type="password" id="staff_password" name="staff_password" placeholder="Enter password" required>
                                <button type="button" class="toggle-password" onclick="togglePasswordVisibility('staff_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select id="staff_role" name="staff_role" required>
                                <option value="cashier">Cashier</option>
                            </select>
                        </div>
                        <button type="submit">Add Staff Member</button>
                    </form>
                </div>

                <div class="access-section">
                    <h3><i class="fas fa-list"></i> Current Cashier Staff</h3>
                    <div id="staffList" class="staff-grid">
                        <!-- Staff members will be loaded here from Firestore -->
                    </div>
                </div>
            </div>

            <!-- Inventory Section -->
            <div id="inventory-section" class="content-section" style="display: none;">
                <h2 style="color: var(--dark-color); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-boxes"></i> Inventory Management
                </h2>
                
                <div class="access-section">
                    <h3><i class="fas fa-list"></i> Current Menu Items Inventory</h3>
                    <div id="menuItemsInventory" class="menu-items-grid">
                        <!-- Menu items will be loaded here from Firestore -->
                    </div>
                </div>
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

        // Real-time monitoring variables
        let staffUnsubscribe = null;
        let transactionsUnsubscribe = null;
        let menuItemsUnsubscribe = null;

        // Helper function to escape HTML for safe rendering
        function escapeHtml(unsafe) {
            if (typeof unsafe !== 'string') return unsafe;
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Mobile menu functionality
        function toggleMobileMenu() {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.querySelector('.mobile-menu-toggle');
            
            if (sidebar.classList.contains('expanded')) {
                sidebar.classList.remove('expanded');
                sidebar.classList.add('collapsed');
                toggleBtn.innerHTML = '<i class="fas fa-bars"></i> Show Menu';
            } else {
                sidebar.classList.remove('collapsed');
                sidebar.classList.add('expanded');
                toggleBtn.innerHTML = '<i class="fas fa-times"></i> Hide Menu';
            }
        }

        // Function to show/hide sections
        function showSection(sectionName) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Show selected section
            document.getElementById(sectionName + '-section').style.display = 'block';
            
            // Load data when section is shown with real-time updates
            if (sectionName === 'inventory') {
                loadMenuItemsInventory();
            }
            
            if (sectionName === 'staff') {
                loadStaffMembers();
            }

            // Close mobile menu after navigation
            if (window.innerWidth <= 768) {
                toggleMobileMenu();
            }
        }

        // Function to fetch dashboard statistics
        async function fetchDashboardStats() {
            try {
                // Fetch today's transactions
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                const transactionsSnapshot = await db.collection('transactions')
                    .where('timestamp', '>=', today)
                    .get();
                
                let totalSales = 0;
                let totalOrders = transactionsSnapshot.size;
                
                transactionsSnapshot.forEach(doc => {
                    const data = doc.data();
                    totalSales += data.total || 0;
                });
                
                // Fetch active cashier staff count
                const staffSnapshot = await db.collection('staff')
                    .where('role', '==', 'cashier')
                    .where('isActive', '==', true)
                    .get();
                const activeStaffCount = staffSnapshot.size;
                
                // Fetch low stock items count
                const menuSnapshot = await db.collection('menu_items')
                    .where('stock', '<=', 5)
                    .get();
                const lowStockCount = menuSnapshot.size;
                
                // Update dashboard
                document.getElementById('totalSales').textContent = '₱' + totalSales.toFixed(2);
                document.getElementById('totalOrders').textContent = totalOrders;
                document.getElementById('activeStaff').textContent = activeStaffCount;
                document.getElementById('lowStock').textContent = lowStockCount;
                
            } catch (error) {
                console.error('Error fetching dashboard stats:', error);
            }
        }

        // Function to add menu item to Firestore
        async function addMenuItemToFirestore(menuItem) {
            try {
                const docRef = await db.collection('menu_items').add(menuItem);
                console.log('Menu item added with ID:', docRef.id);
                return docRef.id;
            } catch (error) {
                console.error('Error adding menu item:', error);
                throw error;
            }
        }

        // Function to load menu items for inventory with edit functionality
        async function loadMenuItemsInventory() {
            try {
                const menuItemsInventory = document.getElementById('menuItemsInventory');
                menuItemsInventory.innerHTML = '<p>Loading menu items...</p>';
                
                const snapshot = await db.collection('menu_items').get();
                
                if (snapshot.empty) {
                    menuItemsInventory.innerHTML = '<p>No menu items found.</p>';
                    return;
                }
                
                let menuItemsHTML = '';
                snapshot.forEach(doc => {
                    const item = doc.data();
                    const stockStatus = item.stock <= 5 ? 'low-stock' : 'in-stock';
                    const statusText = item.stock <= 5 ? 'Low Stock' : 'In Stock';
                    
                    menuItemsHTML += `
                        <div class="menu-item-card ${stockStatus}">
                            <div class="menu-item-emoji">${item.image}</div>
                            <div class="menu-item-name">${item.name}</div>
                            <div class="menu-item-price">₱${item.price.toFixed(2)}</div>
                            <div class="menu-item-category">${item.category}</div>
                            <div class="menu-item-stock ${stockStatus}">Stock: ${item.stock} (${statusText})</div>
                            <div class="inventory-actions">
                                <button class="edit-stock-btn" onclick="openEditStockModal('${doc.id}', '${item.name}', ${item.stock})">
                                    <i class="fas fa-edit"></i> Edit Stock
                                </button>
                                <button class="remove-item-btn" onclick="openRemoveItemModal('${doc.id}', '${item.name}')">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                menuItemsInventory.innerHTML = menuItemsHTML;
                
            } catch (error) {
                console.error('Error loading menu items:', error);
                document.getElementById('menuItemsInventory').innerHTML = '<p>Error loading menu items.</p>';
            }
        }

        // Function to create staff member in Firestore with better error handling
        async function createStaffMember(staffData) {
            try {
                // Validate required fields
                if (!staffData.name || !staffData.username || !staffData.password) {
                    throw new Error('Name, username, and password are required');
                }

                const staffDoc = {
                    name: staffData.name.trim(),
                    phone: staffData.phone || '',
                    username: staffData.username.trim(),
                    email: staffData.email || '',
                    role: staffData.role || 'cashier',
                    password: staffData.password,
                    isActive: false,
                    createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                    lastActivity: firebase.firestore.FieldValue.serverTimestamp()
                };
                
                console.log('Creating staff member:', staffDoc);
                const docRef = await db.collection('staff').add(staffDoc);
                console.log("Staff member added to Firestore with ID:", docRef.id);
                return docRef.id;
                
            } catch (error) {
                console.error('Error creating staff member:', error);
                throw error;
            }
        }

        // Function to update staff member in Firestore
        async function updateStaffMember(staffId, staffData) {
            try {
                // If password is provided, include it in the update
                // If password is empty, don't update the password field
                const updateData = {
                    name: staffData.name,
                    phone: staffData.phone,
                    email: staffData.email,
                    username: staffData.username,
                    role: staffData.role,
                    lastActivity: firebase.firestore.FieldValue.serverTimestamp()
                };
                
                // Only update password if a new one is provided
                if (staffData.password && staffData.password.trim() !== '') {
                    updateData.password = staffData.password;
                }
                
                await db.collection('staff').doc(staffId).update(updateData);
                console.log("Staff member updated:", staffId);
                return staffId;
            } catch (error) {
                console.error('Error updating staff member:', error);
                throw error;
            }
        }

        // Function to load staff members from Firestore (only cashiers)
        async function loadStaffMembers() {
            try {
                console.log('Loading staff members...');
                const staffList = document.getElementById('staffList');
                staffList.innerHTML = '<p>Loading cashier staff...</p>';
                
                // Only fetch staff with role 'cashier'
                const snapshot = await db.collection('staff')
                    .where('role', '==', 'cashier')
                    .get();
                
                console.log('Cashier staff found:', snapshot.size);
                
                if (snapshot.empty) {
                    console.log('No cashier staff members found in Firestore');
                    staffList.innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #666;">
                            <i class="fas fa-users" style="font-size: 3em; margin-bottom: 20px; color: #ccc;"></i>
                            <h3>No Cashier Staff Found</h3>
                            <p>Add your first cashier staff member using the form above.</p>
                        </div>
                    `;
                    return;
                }
                
                let staffHTML = '';
                let staffCount = 0;
                
                snapshot.forEach(doc => {
                    staffCount++;
                    const staff = doc.data();
                    console.log(`Staff ${staffCount}:`, staff);
                    
                    // Safe data handling with fallbacks
                    const staffName = staff.name || 'Unknown Name';
                    const staffUsername = staff.username || 'No Username';
                    const staffEmail = staff.email || 'No Email';
                    const staffPhone = staff.phone || 'No Phone';
                    const staffRole = staff.role || 'cashier';
                    const isActive = staff.isActive || false;
                    const lastActivity = staff.lastActivity ? staff.lastActivity.toDate() : null;
                    
                    staffHTML += `
                        <div class="staff-card">
                            <div class="staff-avatar">
                                ${getRoleEmoji(staffRole)}
                            </div>
                            <div class="staff-info">
                                <div class="staff-name">${escapeHtml(staffName)}</div>
                                <div class="staff-role cashier-badge">${staffRole.toUpperCase()}</div>
                                <div class="staff-details">
                                    <div><i class="fas fa-user"></i> ${escapeHtml(staffUsername)}</div>
                                    <div><i class="fas fa-envelope"></i> ${escapeHtml(staffEmail)}</div>
                                    <div><i class="fas fa-phone"></i> ${escapeHtml(staffPhone)}</div>
                                </div>
                                <div class="staff-status-section">
                                    <span class="online-indicator ${isActive ? 'online' : 'offline'}"></span>
                                    <span class="status-badge ${isActive ? 'status-active' : 'status-offline'}">
                                        ${isActive ? 'Active Now' : 'Offline'}
                                    </span>
                                    ${lastActivity ? `<div class="last-activity">Last active: ${formatTimeAgo(lastActivity)}</div>` : ''}
                                </div>
                            </div>
                            <div class="staff-actions">
                                <button class="edit-btn" onclick="openEditStaffModal('${doc.id}', '${escapeHtml(staffName)}', '${escapeHtml(staffPhone)}', '${escapeHtml(staffEmail)}', '${escapeHtml(staffUsername)}', '${staffRole}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                console.log(`Rendered ${staffCount} cashier staff members`);
                staffList.innerHTML = staffHTML;
                
            } catch (error) {
                console.error('Error loading staff members:', error);
                document.getElementById('staffList').innerHTML = `
                    <div style="text-align: center; padding: 40px; color: var(--danger-color);">
                        <i class="fas fa-exclamation-triangle" style="font-size: 3em; margin-bottom: 20px;"></i>
                        <h3>Error Loading Staff Members</h3>
                        <p>${error.message}</p>
                        <button onclick="loadStaffMembers()" style="margin-top: 15px; padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer;">
                            <i class="fas fa-redo"></i> Try Again
                        </button>
                    </div>
                `;
            }
        }

        // Function to toggle password visibility
        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleButton = passwordInput.nextElementSibling;
            const icon = toggleButton.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Function to open edit staff modal
        function openEditStaffModal(staffId, name, phone, email, username, role) {
            document.getElementById('editStaffId').value = staffId;
            document.getElementById('editStaffName').value = name;
            document.getElementById('editStaffPhone').value = phone;
            document.getElementById('editStaffEmail').value = email;
            document.getElementById('editStaffUsername').value = username;
            document.getElementById('editStaffRole').value = role;
            document.getElementById('editStaffPassword').value = ''; // Clear password field
            
            document.getElementById('editStaffModal').classList.add('active');
        }

        // Function to close edit staff modal
        function closeEditStaffModal() {
            document.getElementById('editStaffModal').classList.remove('active');
        }

        // Function to open edit stock modal
        function openEditStockModal(itemId, itemName, currentStock) {
            document.getElementById('editStockItemId').value = itemId;
            document.getElementById('editStockItemName').value = itemName;
            document.getElementById('editStockCurrentStock').value = currentStock;
            document.getElementById('editStockNewStock').value = currentStock;
            
            document.getElementById('editStockModal').classList.add('active');
        }

        // Function to close edit stock modal
        function closeEditStockModal() {
            document.getElementById('editStockModal').classList.remove('active');
        }

        // Function to update stock in Firestore
        async function updateStockInFirestore(itemId, newStock) {
            try {
                await db.collection('menu_items').doc(itemId).update({
                    stock: parseInt(newStock)
                });
                console.log('Stock updated for item:', itemId);
                return true;
            } catch (error) {
                console.error('Error updating stock:', error);
                throw error;
            }
        }

        // Function to open remove item modal
        function openRemoveItemModal(itemId, itemName) {
            document.getElementById('removeItemId').value = itemId;
            document.getElementById('removeItemName').textContent = itemName;
            
            document.getElementById('removeItemModal').classList.add('active');
        }

        // Function to close remove item modal
        function closeRemoveItemModal() {
            document.getElementById('removeItemModal').classList.remove('active');
        }

        // Function to remove item from Firestore
        async function removeItemFromFirestore(itemId) {
            try {
                await db.collection('menu_items').doc(itemId).delete();
                console.log('Item removed:', itemId);
                return true;
            } catch (error) {
                console.error('Error removing item:', error);
                throw error;
            }
        }

        function getRoleEmoji(role) {
            const emojis = {
                'owner': '👑',
                'manager': '💼',
                'cashier': '🧾'
            };
            return emojis[role] || '👤';
        }

        // Time formatting helper function
        function formatTimeAgo(date) {
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
            return `${Math.floor(diffInSeconds / 86400)} days ago`;
        }

        // Real-time staff status monitoring (cashiers only)
        function startRealTimeStaffMonitoring() {
            if (staffUnsubscribe) {
                staffUnsubscribe(); // Clean up previous listener
            }
            
            try {
                // Only monitor cashiers
                staffUnsubscribe = db.collection('staff')
                    .where('role', '==', 'cashier')
                    .onSnapshot((snapshot) => {
                        console.log('Real-time cashier staff update received:', snapshot.size, 'cashiers');
                        updateActiveStaffCount();
                        if (document.getElementById('staff-section').style.display !== 'none') {
                            loadStaffMembers();
                        }
                    }, (error) => {
                        console.error('Error in real-time staff monitoring:', error);
                        // Try to restart monitoring after a delay
                        setTimeout(() => {
                            startRealTimeStaffMonitoring();
                        }, 5000);
                    });
            } catch (error) {
                console.error('Error starting real-time staff monitoring:', error);
            }
        }

        // Function to update active staff count in real-time (cashiers only)
        async function updateActiveStaffCount() {
            try {
                const staffSnapshot = await db.collection('staff')
                    .where('role', '==', 'cashier')
                    .where('isActive', '==', true)
                    .get();
                const activeStaffCount = staffSnapshot.size;
                console.log('Active cashier count:', activeStaffCount);
                document.getElementById('activeStaff').textContent = activeStaffCount;
            } catch (error) {
                console.error('Error updating active staff count:', error);
                document.getElementById('activeStaff').textContent = '0';
            }
        }

        // Real-time transactions monitoring for dashboard
        function startRealTimeTransactionsMonitoring() {
            if (transactionsUnsubscribe) {
                transactionsUnsubscribe();
            }
            
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            transactionsUnsubscribe = db.collection('transactions')
                .where('timestamp', '>=', today)
                .onSnapshot((snapshot) => {
                    console.log('Real-time transactions update received');
                    updateDashboardStats();
                }, (error) => {
                    console.error('Error in real-time transactions monitoring:', error);
                });
        }

        // Real-time menu items monitoring for inventory
        function startRealTimeMenuItemsMonitoring() {
            if (menuItemsUnsubscribe) {
                menuItemsUnsubscribe();
            }
            
            menuItemsUnsubscribe = db.collection('menu_items')
                .onSnapshot((snapshot) => {
                    console.log('Real-time menu items update received');
                    updateLowStockCount();
                    
                    // Only update inventory display if we're on the inventory page
                    if (document.getElementById('inventory-section').style.display !== 'none') {
                        loadMenuItemsInventory();
                    }
                }, (error) => {
                    console.error('Error in real-time menu items monitoring:', error);
                });
        }

        // Enhanced dashboard stats function
        async function updateDashboardStats() {
            try {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                const transactionsSnapshot = await db.collection('transactions')
                    .where('timestamp', '>=', today)
                    .get();
                
                let totalSales = 0;
                let totalOrders = transactionsSnapshot.size;
                
                transactionsSnapshot.forEach(doc => {
                    const data = doc.data();
                    totalSales += data.total || 0;
                });
                
                // Update dashboard in real-time
                document.getElementById('totalSales').textContent = '₱' + totalSales.toFixed(2);
                document.getElementById('totalOrders').textContent = totalOrders;
                
            } catch (error) {
                console.error('Error updating dashboard stats:', error);
            }
        }

        // Enhanced low stock count function
        async function updateLowStockCount() {
            try {
                const menuSnapshot = await db.collection('menu_items')
                    .where('stock', '<=', 5)
                    .get();
                const lowStockCount = menuSnapshot.size;
                document.getElementById('lowStock').textContent = lowStockCount;
            } catch (error) {
                console.error('Error updating low stock count:', error);
            }
        }

        // Function to update current user's status to active
        async function updateCurrentUserStatus() {
            const staffId = '<?php echo isset($_SESSION["staff_id"]) ? $_SESSION["staff_id"] : ""; ?>';
            
            if (staffId) {
                try {
                    await db.collection('staff').doc(staffId).update({
                        isActive: true,
                        lastLogin: firebase.firestore.FieldValue.serverTimestamp(),
                        lastActivity: firebase.firestore.FieldValue.serverTimestamp()
                    });
                    
                    console.log('User status updated to active');
                    
                } catch (error) {
                    console.error('Error updating user status:', error);
                }
            }
        }

        // Enhanced logout function with real-time update
        async function handleLogout() {
            const staffId = '<?php echo isset($_SESSION["staff_id"]) ? $_SESSION["staff_id"] : ""; ?>';
            
            if (staffId) {
                try {
                    await db.collection('staff').doc(staffId).update({
                        isActive: false,
                        lastLogout: firebase.firestore.FieldValue.serverTimestamp(),
                        lastActivity: firebase.firestore.FieldValue.serverTimestamp()
                    });
                    
                    // Force immediate update
                    setTimeout(() => {
                        updateActiveStaffCount();
                    }, 500);
                    
                } catch (error) {
                    console.error('Error updating staff status on logout:', error);
                }
            }
            
            window.location.href = 'admin.php?action=logout';
        }

        // Function to initialize all real-time listeners
        function initializeRealTimeListeners() {
            startRealTimeStaffMonitoring();
            startRealTimeTransactionsMonitoring();
            startRealTimeMenuItemsMonitoring();
            
            // Update current user status to active
            updateCurrentUserStatus();
            
            // Initial data load
            fetchDashboardStats();
        }

        // Function to clean up real-time listeners
        function cleanupRealTimeListeners() {
            if (staffUnsubscribe) {
                staffUnsubscribe();
                staffUnsubscribe = null;
            }
            if (transactionsUnsubscribe) {
                transactionsUnsubscribe();
                transactionsUnsubscribe = null;
            }
            if (menuItemsUnsubscribe) {
                menuItemsUnsubscribe();
                menuItemsUnsubscribe = null;
            }
        }

        // Debug function to check staff data
        async function debugStaffData() {
            try {
                console.log('=== DEBUG STAFF DATA ===');
                
                // Get all staff
                const snapshot = await db.collection('staff').get();
                console.log('Total staff documents:', snapshot.size);
                
                if (snapshot.empty) {
                    console.log('No staff documents found in Firestore');
                    return;
                }
                
                snapshot.forEach((doc, index) => {
                    console.log(`Staff ${index + 1}:`, {
                        id: doc.id,
                        data: doc.data()
                    });
                });
                
                // Check cashiers specifically
                const cashiersSnapshot = await db.collection('staff')
                    .where('role', '==', 'cashier')
                    .get();
                console.log('Cashier staff count:', cashiersSnapshot.size);
                
            } catch (error) {
                console.error('Debug error:', error);
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize mobile menu behavior
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
                if (mobileToggle) {
                    mobileToggle.style.display = 'flex';
                }
            } else {
                if (mobileToggle) {
                    mobileToggle.style.display = 'none';
                }
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    // Desktop - always show sidebar
                    sidebar.classList.remove('collapsed', 'expanded');
                    if (mobileToggle) {
                        mobileToggle.style.display = 'none';
                    }
                } else {
                    // Mobile - initially hide sidebar
                    if (mobileToggle) {
                        mobileToggle.style.display = 'flex';
                    }
                    if (!sidebar.classList.contains('expanded')) {
                        sidebar.classList.add('collapsed');
                    }
                }
            });

            // Menu form handling
            const addMenuItemForm = document.getElementById('addMenuItemForm');
            if (addMenuItemForm) {
                addMenuItemForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const menuItem = {
                        name: document.getElementById('item_name').value,
                        price: parseFloat(document.getElementById('item_price').value),
                        category: document.getElementById('item_category').value,
                        description: document.getElementById('item_description').value,
                        image: document.getElementById('item_emoji').value,
                        stock: parseInt(document.getElementById('item_stock').value)
                    };
                    
                    try {
                        await addMenuItemToFirestore(menuItem);
                        alert('Menu item added successfully!');
                        document.getElementById('addMenuItemForm').reset();
                    } catch (error) {
                        alert('Error adding menu item: ' + error.message);
                    }
                });
            }

            // Staff form handling with better validation
            const addStaffForm = document.getElementById('addStaffForm');
            if (addStaffForm) {
                addStaffForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const staffData = {
                        name: document.getElementById('staff_name').value,
                        phone: document.getElementById('staff_phone').value,
                        email: document.getElementById('staff_email').value,
                        username: document.getElementById('staff_username').value,
                        password: document.getElementById('staff_password').value,
                        role: document.getElementById('staff_role').value
                    };
                    
                    // Basic validation
                    if (!staffData.name.trim()) {
                        alert('Please enter a full name');
                        return;
                    }
                    if (!staffData.username.trim()) {
                        alert('Please enter a username');
                        return;
                    }
                    if (!staffData.password) {
                        alert('Please enter a password');
                        return;
                    }
                    
                    try {
                        const submitBtn = addStaffForm.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                        submitBtn.disabled = true;
                        
                        await createStaffMember(staffData);
                        alert('Staff member added successfully!');
                        document.getElementById('addStaffForm').reset();
                        
                        // Reload staff list to show the new member
                        if (document.getElementById('staff-section').style.display !== 'none') {
                            loadStaffMembers();
                        }
                        
                    } catch (error) {
                        console.error('Error adding staff member:', error);
                        alert('Error adding staff member: ' + error.message);
                    } finally {
                        const submitBtn = addStaffForm.querySelector('button[type="submit"]');
                        submitBtn.innerHTML = 'Add Staff Member';
                        submitBtn.disabled = false;
                    }
                });
            }

            // Edit staff form handling
            const editStaffForm = document.getElementById('editStaffForm');
            if (editStaffForm) {
                editStaffForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const staffId = document.getElementById('editStaffId').value;
                    const staffData = {
                        name: document.getElementById('editStaffName').value,
                        phone: document.getElementById('editStaffPhone').value,
                        email: document.getElementById('editStaffEmail').value,
                        username: document.getElementById('editStaffUsername').value,
                        role: document.getElementById('editStaffRole').value,
                        password: document.getElementById('editStaffPassword').value
                    };
                    
                    try {
                        await updateStaffMember(staffId, staffData);
                        alert('Staff member updated successfully!');
                        closeEditStaffModal();
                        // No need to call loadStaffMembers() - real-time listener will handle it
                    } catch (error) {
                        alert('Error updating staff member: ' + error.message);
                    }
                });
            }

            // Edit stock form handling
            const editStockForm = document.getElementById('editStockForm');
            if (editStockForm) {
                editStockForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const itemId = document.getElementById('editStockItemId').value;
                    const newStock = parseInt(document.getElementById('editStockNewStock').value);
                    
                    try {
                        await updateStockInFirestore(itemId, newStock);
                        alert('Stock updated successfully!');
                        closeEditStockModal();
                        // No need to call loadMenuItemsInventory() - real-time listener will handle it
                    } catch (error) {
                        alert('Error updating stock: ' + error.message);
                    }
                });
            }

            // Remove item form handling
            const removeItemForm = document.getElementById('removeItemForm');
            if (removeItemForm) {
                removeItemForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const itemId = document.getElementById('removeItemId').value;
                    
                    try {
                        await removeItemFromFirestore(itemId);
                        alert('Item removed successfully!');
                        closeRemoveItemModal();
                        // No need to call loadMenuItemsInventory() - real-time listener will handle it
                    } catch (error) {
                        alert('Error removing item: ' + error.message);
                    }
                });
            }

            // Make dashboard stats clickable
            document.getElementById('totalSales').closest('.stat-card').classList.add('clickable');
            document.getElementById('totalOrders').closest('.stat-card').classList.add('clickable');
            document.getElementById('activeStaff').closest('.stat-card').classList.add('clickable');
            document.getElementById('lowStock').closest('.stat-card').classList.add('clickable');

            // Add click event listeners to dashboard stats
            document.getElementById('totalSales').closest('.stat-card').addEventListener('click', function() {
                showSection('menu');
            });

            document.getElementById('totalOrders').closest('.stat-card').addEventListener('click', function() {
                showSection('menu');
            });

            document.getElementById('activeStaff').closest('.stat-card').addEventListener('click', function() {
                showSection('staff');
            });

            document.getElementById('lowStock').closest('.stat-card').addEventListener('click', function() {
                showSection('inventory');
            });

            // Initialize dashboard with real-time updates
            initializeRealTimeListeners();
            showSection('dashboard');
            
            // Set up cleanup when page unloads
            window.addEventListener('beforeunload', cleanupRealTimeListeners);
        });
    </script>

    <!-- Edit Staff Modal -->
    <div id="editStaffModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-edit"></i> Edit Staff Member</h3>
                <button class="modal-close" onclick="closeEditStaffModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editStaffForm">
                <div class="modal-body">
                    <input type="hidden" id="editStaffId">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" id="editStaffName" name="editStaffName" placeholder="Enter full name" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" id="editStaffPhone" name="editStaffPhone" placeholder="Enter phone number" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" id="editStaffEmail" name="editStaffEmail" placeholder="Enter email address" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" id="editStaffUsername" name="editStaffUsername" placeholder="Enter username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-input-container">
                            <input type="password" id="editStaffPassword" name="editStaffPassword" placeholder="Enter new password (leave blank to keep current)">
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('editStaffPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="password-hint">Leave blank to keep current password</small>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select id="editStaffRole" name="editStaffRole" required>
                            <option value="cashier">Cashier</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeEditStaffModal()">Cancel</button>
                    <button type="submit" class="modal-btn modal-btn-primary">Update Staff Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Stock Modal -->
    <div id="editStockModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-boxes"></i> Edit Stock</h3>
                <button class="modal-close" onclick="closeEditStockModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editStockForm">
                <div class="modal-body">
                    <input type="hidden" id="editStockItemId">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" id="editStockItemName" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="form-group">
                        <label>Current Stock</label>
                        <input type="number" id="editStockCurrentStock" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="form-group">
                        <label>New Stock Quantity</label>
                        <input type="number" id="editStockNewStock" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeEditStockModal()">Cancel</button>
                    <button type="submit" class="modal-btn modal-btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Remove Item Modal -->
    <div id="removeItemModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-trash"></i> Remove Item</h3>
                <button class="modal-close" onclick="closeRemoveItemModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="removeItemForm">
                <div class="modal-body">
                    <input type="hidden" id="removeItemId">
                    <div class="warning-message">
                        <i class="fas fa-exclamation-triangle" style="color: var(--danger-color); font-size: 2em; margin-bottom: 15px;"></i>
                        <p>Are you sure you want to remove the following item from the menu?</p>
                        <div class="item-to-remove">
                            <strong id="removeItemName"></strong>
                        </div>
                        <p style="color: var(--danger-color); font-weight: 600; margin-top: 15px;">
                            This action cannot be undone!
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeRemoveItemModal()">Cancel</button>
                    <button type="submit" class="modal-btn modal-btn-danger">Remove Item</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>