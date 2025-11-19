<?php
session_start();

// Check if user is logged in and is owner
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
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
    <title>Cafe Management - Owner</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/owner.css">
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
                <div class="role-badge owner-badge">
                    Owner
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
                        <a href="#reports" onclick="showSection('reports')" style="display: block; padding: 15px; background: #f8f9fa; border-radius: 10px; text-decoration: none; color: var(--text-color); font-weight: 500; transition: all 0.3s;">
                            <i class="fas fa-chart-line"></i> Sales Reports
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
                    <i class="fas fa-tachometer-alt"></i> Owner Dashboard
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
                        <div class="stat-label">Active Staff</div>
                    </div>
                    <div class="stat-card clickable">
                        <i class="fas fa-box"></i>
                        <div class="stat-number" id="lowStock">0</div>
                        <div class="stat-label">Low Stock Items</div>
                    </div>
                </div>
            </div>

            <!-- Sales Reports Section -->
            <div id="reports-section" class="content-section" style="display: none;">
                <h2 style="color: var(--dark-color); margin-bottom: 25px; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-chart-line"></i> Sales Reports
                </h2>
                
                <div class="reports-controls">
                    <div class="date-navigation">
                        <div class="date-controls">
                            <div class="date-nav">
                                <button class="calendar-btn" onclick="changeDate('day', -1)">
                                    <i class="fas fa-chevron-left"></i> <span class="btn-text">Prev Day</span>
                                </button>
                                <div class="current-date" id="currentDate">Loading...</div>
                                <button class="calendar-btn" onclick="changeDate('day', 1)">
                                    <span class="btn-text">Next Day</span> <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="date-nav">
                                <button class="calendar-btn" onclick="changeDate('month', -1)">
                                    <i class="fas fa-chevron-left"></i> <span class="btn-text">Prev Month</span>
                                </button>
                                <div class="current-month" id="currentMonth">Loading...</div>
                                <button class="calendar-btn" onclick="changeDate('month', 1)">
                                    <span class="btn-text">Next Month</span> <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="date-nav">
                                <button class="calendar-btn" onclick="changeDate('year', -1)">
                                    <i class="fas fa-chevron-left"></i> <span class="btn-text">Prev Year</span>
                                </button>
                                <div class="current-year" id="currentYear">Loading...</div>
                                <button class="calendar-btn" onclick="changeDate('year', 1)">
                                    <span class="btn-text">Next Year</span> <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="date-actions">
                        <button class="calendar-btn today-btn" onclick="setCurrentDate()">
                            <i class="fas fa-sync"></i> <span class="btn-text">Today</span>
                        </button>
                        <select id="reportType" onchange="changeReportType()" class="report-type-select">
                            <option value="daily">Daily Report</option>
                            <option value="weekly">Weekly Report</option>
                            <option value="monthly">Monthly Report</option>
                            <option value="yearly">Yearly Report</option>
                        </select>
                    </div>
                </div>

                <div class="charts-container">
                    <div class="chart-section">
                        <div class="chart-header">
                            <h3 id="dailyChartTitle">Hourly Sales</h3>
                            <div class="chart-total" id="dailyTotal">Total: ₱0.00</div>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="dailyChart"></canvas>
                            <div class="no-data-message" id="dailyNoData" style="display: none;">
                                <i class="fas fa-chart-bar"></i>
                                <p>No sales data available</p>
                            </div>
                        </div>
                    </div>

                    <div class="chart-section">
                        <div class="chart-header">
                            <h3 id="weeklyChartTitle">Weekly Sales</h3>
                            <div class="chart-total" id="weeklyTotal">Total: ₱0.00</div>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="weeklyChart"></canvas>
                            <div class="no-data-message" id="weeklyNoData" style="display: none;">
                                <i class="fas fa-chart-line"></i>
                                <p>No sales data available</p>
                            </div>
                        </div>
                    </div>

                    <div class="chart-section">
                        <div class="chart-header">
                            <h3 id="monthlyChartTitle">Monthly Overview</h3>
                            <div class="chart-total" id="monthlyTotal">Total: ₱0.00</div>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="monthlyChart"></canvas>
                            <div class="no-data-message" id="monthlyNoData" style="display: none;">
                                <i class="fas fa-chart-pie"></i>
                                <p>No sales data available</p>
                            </div>
                        </div>
                    </div>

                    <div class="chart-section">
                        <div class="chart-header">
                            <h3>Most Popular Items</h3>
                            <div class="chart-total">Top 5 Items</div>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="popularItemsChart"></canvas>
                            <div class="no-data-message" id="popularNoData" style="display: none;">
                                <i class="fas fa-star"></i>
                                <p>No item data available</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="summary-cards">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-value" id="totalOrdersCount">0</div>
                            <div class="summary-label">Total Orders</div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-value" id="totalRevenue">₱0.00</div>
                            <div class="summary-label">Total Revenue</div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-value" id="averageOrder">₱0.00</div>
                            <div class="summary-label">Average Order</div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-value" id="peakHour">-</div>
                            <div class="summary-label">Peak Hour</div>
                        </div>
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
                                <option value="manager">Manager</option>
                                <option value="cashier">Cashier</option>
                            </select>
                        </div>
                        <button type="submit">Add Staff Member</button>
                    </form>
                </div>

                <div class="access-section">
                    <h3><i class="fas fa-list"></i> Current Staff Members</h3>
                    <div id="staffList" class="menu-items-grid">
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

    // Chart variables
    let dailyChart = null;
    let weeklyChart = null;
    let monthlyChart = null;
    let popularItemsChart = null;
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let currentDay = currentDate.getDate();
    let reportType = 'daily'; // 'daily', 'weekly', 'monthly', 'yearly'

    // Real-time monitoring variables
    let staffUnsubscribe = null;
    let transactionsUnsubscribe = null;
    let menuItemsUnsubscribe = null;

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
        if (sectionName === 'reports') {
            updateDateDisplay();
            loadSalesReports();
        }
        
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

    // Function to update date displays
    function updateDateDisplay() {
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];
        
        // Update day display
        document.getElementById('currentDate').textContent = 
            `${currentDay} ${monthNames[currentMonth]} ${currentYear}`;
        
        // Update month display
        document.getElementById('currentMonth').textContent = 
            `${monthNames[currentMonth]} ${currentYear}`;
        
        // Update year display
        document.getElementById('currentYear').textContent = currentYear;
        
        // Update chart titles based on report type
        updateChartTitles();
    }

    // Function to update chart titles based on report type
    function updateChartTitles() {
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];
        
        switch(reportType) {
            case 'daily':
                document.getElementById('dailyChartTitle').textContent = 
                    `Hourly Sales - ${currentDay} ${monthNames[currentMonth]} ${currentYear}`;
                document.getElementById('weeklyChartTitle').textContent = 
                    `Weekly Sales - ${monthNames[currentMonth]} ${currentYear}`;
                document.getElementById('monthlyChartTitle').textContent = 
                    `Monthly Overview - ${monthNames[currentMonth]} ${currentYear}`;
                break;
            case 'weekly':
                document.getElementById('dailyChartTitle').textContent = 
                    `Daily Sales - Week of ${currentDay} ${monthNames[currentMonth]}`;
                document.getElementById('weeklyChartTitle').textContent = 
                    `Monthly Trends - ${currentYear}`;
                document.getElementById('monthlyChartTitle').textContent = 
                    `Yearly Overview - ${currentYear}`;
                break;
            case 'monthly':
                document.getElementById('dailyChartTitle').textContent = 
                    `Daily Sales - ${monthNames[currentMonth]} ${currentYear}`;
                document.getElementById('weeklyChartTitle').textContent = 
                    `Weekly Breakdown - ${monthNames[currentMonth]} ${currentYear}`;
                document.getElementById('monthlyChartTitle').textContent = 
                    `Monthly Comparison`;
                break;
            case 'yearly':
                document.getElementById('dailyChartTitle').textContent = 
                    `Monthly Sales - ${currentYear}`;
                document.getElementById('weeklyChartTitle').textContent = 
                    `Quarterly Trends`;
                document.getElementById('monthlyChartTitle').textContent = 
                    `Category Performance`;
                break;
        }
    }

    // Function to change date (day, month, or year)
    function changeDate(type, direction) {
        try {
            switch(type) {
                case 'day':
                    currentDate.setDate(currentDate.getDate() + direction);
                    currentDay = currentDate.getDate();
                    currentMonth = currentDate.getMonth();
                    currentYear = currentDate.getFullYear();
                    break;
                case 'month':
                    currentDate.setMonth(currentDate.getMonth() + direction);
                    currentDay = currentDate.getDate();
                    currentMonth = currentDate.getMonth();
                    currentYear = currentDate.getFullYear();
                    break;
                case 'year':
                    currentDate.setFullYear(currentDate.getFullYear() + direction);
                    currentDay = currentDate.getDate();
                    currentMonth = currentDate.getMonth();
                    currentYear = currentDate.getFullYear();
                    break;
            }
            updateDateDisplay();
            loadSalesReports();
        } catch (error) {
            console.error('Error changing date:', error);
            alert('Error changing date: ' + error.message);
        }
    }

    // Function to set current date
    function setCurrentDate() {
        currentDate = new Date();
        currentDay = currentDate.getDate();
        currentMonth = currentDate.getMonth();
        currentYear = currentDate.getFullYear();
        updateDateDisplay();
        loadSalesReports();
    }

    // Function to change report type
    function changeReportType() {
        reportType = document.getElementById('reportType').value;
        updateDateDisplay();
        loadSalesReports();
    }

    // Enhanced function to load sales reports with summary data
    async function loadSalesReports() {
        try {
            showLoadingState();
            
            let startDate, endDate;
            
            // Set date range based on report type
            switch(reportType) {
                case 'daily':
                    startDate = new Date(currentYear, currentMonth, currentDay, 0, 0, 0);
                    endDate = new Date(currentYear, currentMonth, currentDay, 23, 59, 59);
                    break;
                case 'weekly':
                    const weekStart = new Date(currentYear, currentMonth, currentDay);
                    weekStart.setDate(currentDay - weekStart.getDay());
                    startDate = new Date(weekStart);
                    startDate.setHours(0, 0, 0, 0);
                    endDate = new Date(weekStart);
                    endDate.setDate(weekStart.getDate() + 6);
                    endDate.setHours(23, 59, 59, 999);
                    break;
                case 'monthly':
                    startDate = new Date(currentYear, currentMonth, 1, 0, 0, 0);
                    endDate = new Date(currentYear, currentMonth + 1, 0, 23, 59, 59);
                    break;
                case 'yearly':
                    startDate = new Date(currentYear, 0, 1, 0, 0, 0);
                    endDate = new Date(currentYear, 11, 31, 23, 59, 59, 999);
                    break;
            }
            
            console.log('Fetching transactions for:', reportType);
            console.log('Date range:', startDate, 'to', endDate);
            
            // Fetch transactions for the selected period
            const transactionsSnapshot = await db.collection('transactions')
                .where('timestamp', '>=', startDate)
                .where('timestamp', '<=', endDate)
                .get();
            
            const transactions = [];
            let totalRevenue = 0;
            let totalOrders = transactionsSnapshot.size;
            
            transactionsSnapshot.forEach(doc => {
                const data = doc.data();
                const transactionDate = data.timestamp.toDate();
                transactions.push({
                    id: doc.id,
                    ...data,
                    date: transactionDate
                });
                totalRevenue += data.total || 0;
            });
            
            console.log(`Found ${transactions.length} transactions`);
            
            // Update summary cards
            updateSummaryCards(transactions, totalRevenue, totalOrders);
            
            // Process data for charts
            if (transactions.length > 0) {
                processChartData(transactions);
                hideNoDataMessages();
            } else {
                displayNoDataMessage();
            }
            
        } catch (error) {
            console.error('Error loading sales reports:', error);
            displayNoDataMessage();
            alert('Error loading sales reports: ' + error.message);
        }
    }

    // Function to update summary cards
    function updateSummaryCards(transactions, totalRevenue, totalOrders) {
        // Update basic summary
        document.getElementById('totalOrdersCount').textContent = totalOrders;
        document.getElementById('totalRevenue').textContent = '₱' + totalRevenue.toFixed(2);
        
        // Calculate average order value
        const averageOrder = totalOrders > 0 ? totalRevenue / totalOrders : 0;
        document.getElementById('averageOrder').textContent = '₱' + averageOrder.toFixed(2);
        
        // Calculate peak hour
        const peakHour = calculatePeakHour(transactions);
        document.getElementById('peakHour').textContent = peakHour;
    }

    // Function to calculate peak hour
    function calculatePeakHour(transactions) {
        if (transactions.length === 0) return '-';
        
        const hourlyCount = new Array(24).fill(0);
        transactions.forEach(transaction => {
            const hour = transaction.date.getHours();
            hourlyCount[hour]++;
        });
        
        const maxCount = Math.max(...hourlyCount);
        const peakHours = hourlyCount
            .map((count, hour) => ({ hour, count }))
            .filter(item => item.count === maxCount)
            .map(item => `${item.hour}:00`);
        
        return peakHours.length > 0 ? peakHours[0] : '-';
    }

    // Function to show loading state
    function showLoadingState() {
        document.querySelectorAll('.no-data-message').forEach(msg => {
            msg.style.display = 'none';
        });
    }

    // Function to hide no data messages
    function hideNoDataMessages() {
        document.querySelectorAll('.no-data-message').forEach(msg => {
            msg.style.display = 'none';
        });
    }

    // Enhanced function to display no data message
    function displayNoDataMessage() {
        // Show no data messages for all charts
        document.querySelectorAll('.no-data-message').forEach(msg => {
            msg.style.display = 'flex';
        });
        
        // Reset chart totals
        document.getElementById('dailyTotal').textContent = 'Total: ₱0.00';
        document.getElementById('weeklyTotal').textContent = 'Total: ₱0.00';
        document.getElementById('monthlyTotal').textContent = 'Total: ₱0.00';
        
        // Reset summary cards
        document.getElementById('totalOrdersCount').textContent = '0';
        document.getElementById('totalRevenue').textContent = '₱0.00';
        document.getElementById('averageOrder').textContent = '₱0.00';
        document.getElementById('peakHour').textContent = '-';
        
        // Destroy existing charts
        if (dailyChart) {
            dailyChart.destroy();
            dailyChart = null;
        }
        if (weeklyChart) {
            weeklyChart.destroy();
            weeklyChart = null;
        }
        if (monthlyChart) {
            monthlyChart.destroy();
            monthlyChart = null;
        }
        if (popularItemsChart) {
            popularItemsChart.destroy();
            popularItemsChart = null;
        }
    }

    // Updated function to process chart data based on report type
    function processChartData(transactions) {
        try {
            // Check if we have any transactions
            if (!transactions || transactions.length === 0) {
                console.log('No transactions found for the selected period');
                displayNoDataMessage();
                return;
            }
            
            console.log(`Processing ${transactions.length} transactions for ${reportType} report`);
            
            // Process data based on report type
            switch(reportType) {
                case 'daily':
                    processDailyReport(transactions);
                    break;
                case 'weekly':
                    processWeeklyReport(transactions);
                    break;
                case 'monthly':
                    processMonthlyReport(transactions);
                    break;
                case 'yearly':
                    processYearlyReport(transactions);
                    break;
                default:
                    console.error('Unknown report type:', reportType);
                    break;
            }
        } catch (error) {
            console.error('Error processing chart data:', error);
            alert('Error processing chart data: ' + error.message);
            displayNoDataMessage();
        }
    }

    // Process daily report data
    function processDailyReport(transactions) {
        // Hourly sales data for the selected day
        const hourlyData = processHourlyData(transactions);
        createDailyChart(hourlyData);
        
        // Weekly data for context
        const weeklyData = processWeeklyData(transactions);
        createWeeklyChart(weeklyData);
        
        // Monthly overview
        const monthlyData = processMonthlyOverview(transactions);
        createMonthlyChart(monthlyData);
        
        // Popular items
        const popularItemsData = processPopularItemsData(transactions);
        createPopularItemsChart(popularItemsData);
    }

    // Process weekly report data
    function processWeeklyReport(transactions) {
        // Daily sales for the week
        const dailyData = processWeeklyDailyData(transactions);
        createDailyChart(dailyData);
        
        // Monthly trends
        const monthlyData = processWeeklyMonthlyData(transactions);
        createWeeklyChart(monthlyData);
        
        // Yearly overview
        const yearlyData = processYearlyOverview(transactions);
        createMonthlyChart(yearlyData);
        
        // Popular items
        const popularItemsData = processPopularItemsData(transactions);
        createPopularItemsChart(popularItemsData);
    }

    // Process monthly report data
    function processMonthlyReport(transactions) {
        // Monthly sales breakdown
        const monthlyData = processMonthlyBreakdown(transactions);
        createDailyChart(monthlyData);
        
        // Weekly breakdown
        const weeklyData = processMonthlyWeeklyData(transactions);
        createWeeklyChart(weeklyData);
        
        // Monthly comparison
        const comparisonData = processMonthlyComparison(transactions);
        createMonthlyChart(comparisonData);
        
        // Popular items
        const popularItemsData = processPopularItemsData(transactions);
        createPopularItemsChart(popularItemsData);
    }

    // Process yearly report data
    function processYearlyReport(transactions) {
        // Yearly sales by month
        const yearlyData = processYearlySalesData(transactions);
        createDailyChart(yearlyData);
        
        // Quarterly trends
        const quarterlyData = processQuarterlyData(transactions);
        createWeeklyChart(quarterlyData);
        
        // Category performance
        const categoryData = processCategoryPerformance(transactions);
        createMonthlyChart(categoryData);
        
        // Popular items
        const popularItemsData = processPopularItemsData(transactions);
        createPopularItemsChart(popularItemsData);
    }

    // Process hourly data for daily report
    function processHourlyData(transactions) {
        const hourlySales = new Array(24).fill(0);
        
        transactions.forEach(transaction => {
            const hour = transaction.date.getHours();
            hourlySales[hour] += transaction.total || 0;
        });
        
        const dailyTotal = hourlySales.reduce((sum, sales) => sum + sales, 0);
        document.getElementById('dailyTotal').textContent = `Total: ₱${dailyTotal.toFixed(2)}`;
        
        return {
            labels: Array.from({length: 24}, (_, i) => `${i}:00`),
            sales: hourlySales
        };
    }

    // Process daily data for weekly report
    function processWeeklyDailyData(transactions) {
        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const dailySales = [0, 0, 0, 0, 0, 0, 0];
        
        transactions.forEach(transaction => {
            const day = transaction.date.getDay();
            dailySales[day] += transaction.total || 0;
        });
        
        const weeklyTotal = dailySales.reduce((sum, sales) => sum + sales, 0);
        document.getElementById('weeklyTotal').textContent = `Total: ₱${weeklyTotal.toFixed(2)}`;
        
        return {
            labels: days,
            sales: dailySales
        };
    }

    // Process monthly breakdown for monthly report
    function processMonthlyBreakdown(transactions) {
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const dailySales = new Array(daysInMonth).fill(0);
        
        console.log(`Processing monthly breakdown for ${daysInMonth} days`);
        
        transactions.forEach(transaction => {
            const day = transaction.date.getDate() - 1;
            if (day >= 0 && day < daysInMonth) {
                dailySales[day] += transaction.total || 0;
            }
        });
        
        const monthlyTotal = dailySales.reduce((sum, sales) => sum + sales, 0);
        document.getElementById('dailyTotal').textContent = `Total: ₱${monthlyTotal.toFixed(2)}`;
        
        // Create labels for each day of the month
        const labels = Array.from({length: daysInMonth}, (_, i) => i + 1);
        
        return {
            labels: labels,
            sales: dailySales
        };
    }

    // Process yearly sales data
    function processYearlySalesData(transactions) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const monthlySales = new Array(12).fill(0);
        
        transactions.forEach(transaction => {
            const month = transaction.date.getMonth();
            monthlySales[month] += transaction.total || 0;
        });
        
        const yearlyTotal = monthlySales.reduce((sum, sales) => sum + sales, 0);
        document.getElementById('dailyTotal').textContent = `Total: ₱${yearlyTotal.toFixed(2)}`;
        
        return {
            labels: months,
            sales: monthlySales
        };
    }

    // Update the existing processWeeklyData function
    function processWeeklyData(transactions) {
        const weeks = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
        const weeklySales = [0, 0, 0, 0, 0];
        
        transactions.forEach(transaction => {
            const week = Math.floor((transaction.date.getDate() - 1) / 7);
            if (week >= 0 && week < 5) {
                weeklySales[week] += transaction.total || 0;
            }
        });
        
        const monthlyTotal = weeklySales.reduce((sum, sales) => sum + sales, 0);
        document.getElementById('weeklyTotal').textContent = `Total: ₱${monthlyTotal.toFixed(2)}`;
        
        return {
            labels: weeks,
            sales: weeklySales
        };
    }

    // Update the existing processMonthlyData function
    function processMonthlyOverview(transactions) {
        const monthlyTotal = transactions.reduce((sum, transaction) => sum + (transaction.total || 0), 0);
        
        const categories = {
            'Espresso': monthlyTotal * 0.4,
            'Frappe': monthlyTotal * 0.3,
            'Milk Tea': monthlyTotal * 0.2,
            'Food': monthlyTotal * 0.1
        };
        
        document.getElementById('monthlyTotal').textContent = `Total: ₱${monthlyTotal.toFixed(2)}`;
        
        return {
            total: monthlyTotal,
            categories: categories
        };
    }

    // Add these new processing functions
    function processWeeklyMonthlyData(transactions) {
        // For weekly report - show last 3 months trend
        const months = [];
        const monthlySales = [];
        
        for (let i = 2; i >= 0; i--) {
            const date = new Date(currentYear, currentMonth - i, 1);
            months.push(date.toLocaleString('default', { month: 'short' }));
            // This would need actual data filtering by month
            monthlySales.push(Math.random() * 10000 + 5000);
        }
        
        const total = monthlySales.reduce((sum, sales) => sum + sales, 0);
        document.getElementById('weeklyTotal').textContent = `Total: ₱${total.toFixed(2)}`;
        
        return {
            labels: months,
            sales: monthlySales
        };
    }

    function processYearlyOverview(transactions) {
        const yearlyTotal = transactions.reduce((sum, transaction) => sum + (transaction.total || 0), 0);
        
        const categories = {
            'Q1': yearlyTotal * 0.25,
            'Q2': yearlyTotal * 0.25,
            'Q3': yearlyTotal * 0.25,
            'Q4': yearlyTotal * 0.25
        };
        
        document.getElementById('monthlyTotal').textContent = `Total: ₱${yearlyTotal.toFixed(2)}`;
        
        return {
            total: yearlyTotal,
            categories: categories
        };
    }

    function processMonthlyWeeklyData(transactions) {
        const weeks = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
        const weeklySales = [0, 0, 0, 0, 0];
        
        transactions.forEach(transaction => {
            const week = Math.floor((transaction.date.getDate() - 1) / 7);
            if (week >= 0 && week < 5) {
                weeklySales[week] += transaction.total || 0;
            }
        });
        
        const total = weeklySales.reduce((sum, sales) => sum + sales, 0);
        document.getElementById('weeklyTotal').textContent = `Total: ₱${total.toFixed(2)}`;
        
        return {
            labels: weeks,
            sales: weeklySales
        };
    }

    function processMonthlyComparison(transactions) {
        const months = [currentMonth - 1, currentMonth];
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];
        const monthlySales = [0, 0];
        
        // This is a simplified version - in real implementation, you'd filter by month
        const currentMonthTotal = transactions.reduce((sum, transaction) => {
            if (transaction.date.getMonth() === currentMonth) {
                return sum + (transaction.total || 0);
            }
            return sum;
        }, 0);
        
        monthlySales[1] = currentMonthTotal;
        monthlySales[0] = currentMonthTotal * 0.8; // Simulated previous month data
        
        const total = monthlySales.reduce((sum, sales) => sum + sales, 0);
        document.getElementById('monthlyTotal').textContent = `Total: ₱${total.toFixed(2)}`;
        
        return {
            labels: months.map(month => monthNames[month]),
            sales: monthlySales
        };
    }

    function processQuarterlyData(transactions) {
        const quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
        const quarterlySales = [0, 0, 0, 0];
        
        transactions.forEach(transaction => {
            const quarter = Math.floor(transaction.date.getMonth() / 3);
            quarterlySales[quarter] += transaction.total || 0;
        });
        
        const total = quarterlySales.reduce((sum, sales) => sum + sales, 0);
        document.getElementById('weeklyTotal').textContent = `Total: ₱${total.toFixed(2)}`;
        
        return {
            labels: quarters,
            sales: quarterlySales
        };
    }

    function processCategoryPerformance(transactions) {
        const categoryTotals = {};
        
        transactions.forEach(transaction => {
            if (transaction.items) {
                transaction.items.forEach(item => {
                    if (!categoryTotals[item.category]) {
                        categoryTotals[item.category] = 0;
                    }
                    categoryTotals[item.category] += item.price * item.quantity;
                });
            }
        });
        
        const total = Object.values(categoryTotals).reduce((sum, sales) => sum + sales, 0);
        document.getElementById('monthlyTotal').textContent = `Total: ₱${total.toFixed(2)}`;
        
        return {
            total: total,
            categories: categoryTotals
        };
    }

    // Process popular items data
    function processPopularItemsData(transactions) {
        const itemSales = {};
        
        transactions.forEach(transaction => {
            if (transaction.items) {
                transaction.items.forEach(item => {
                    if (!itemSales[item.name]) {
                        itemSales[item.name] = 0;
                    }
                    itemSales[item.name] += item.quantity;
                });
            }
        });
        
        // Sort by quantity sold (descending)
        const sortedItems = Object.entries(itemSales)
            .sort(([,a], [,b]) => b - a)
            .slice(0, 5); // Top 5 items
        
        return {
            labels: sortedItems.map(([name]) => name),
            quantities: sortedItems.map(([,quantity]) => quantity)
        };
    }

    // Create daily chart
    function createDailyChart(data) {
        const ctx = document.getElementById('dailyChart').getContext('2d');
        
        if (dailyChart) {
            dailyChart.destroy();
        }
        
        // Check if we have data
        if (!data || !data.labels || !data.sales) {
            console.error('Invalid data for daily chart:', data);
            displayNoDataMessage();
            return;
        }
        
        dailyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Sales (₱)',
                    data: data.sales,
                    backgroundColor: 'rgba(139, 69, 19, 0.6)',
                    borderColor: 'rgba(139, 69, 19, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    }

    // Create weekly chart
    function createWeeklyChart(data) {
        const ctx = document.getElementById('weeklyChart').getContext('2d');
        
        if (weeklyChart) {
            weeklyChart.destroy();
        }
        
        // Check if we have data
        if (!data || !data.labels || !data.sales) {
            console.error('Invalid data for weekly chart:', data);
            displayNoDataMessage();
            return;
        }
        
        weeklyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Sales (₱)',
                    data: data.sales,
                    backgroundColor: 'rgba(210, 105, 30, 0.2)',
                    borderColor: 'rgba(210, 105, 30, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    }

    // Create monthly chart
    function createMonthlyChart(data) {
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        
        if (monthlyChart) {
            monthlyChart.destroy();
        }
        
        // Check if we have data
        if (!data || !data.categories) {
            console.error('Invalid data for monthly chart:', data);
            displayNoDataMessage();
            return;
        }
        
        monthlyChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(data.categories),
                datasets: [{
                    data: Object.values(data.categories),
                    backgroundColor: [
                        'rgba(139, 69, 19, 0.8)',
                        'rgba(210, 105, 30, 0.8)',
                        'rgba(244, 162, 97, 0.8)',
                        'rgba(139, 69, 19, 0.5)',
                        'rgba(210, 105, 30, 0.5)',
                        'rgba(244, 162, 97, 0.5)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Create popular items chart
    function createPopularItemsChart(data) {
        const ctx = document.getElementById('popularItemsChart').getContext('2d');
        
        if (popularItemsChart) {
            popularItemsChart.destroy();
        }
        
        // Check if we have data
        if (!data || !data.labels || !data.quantities) {
            console.error('Invalid data for popular items chart:', data);
            displayNoDataMessage();
            return;
        }
        
        popularItemsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Quantity Sold',
                    data: data.quantities,
                    backgroundColor: 'rgba(40, 167, 69, 0.6)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
            }
        });
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
            
            // Fetch active staff count
            const staffSnapshot = await db.collection('staff')
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

    // Function to create staff member in Firestore
    async function createStaffMember(staffData) {
        try {
            const staffDoc = {
                name: staffData.name,
                phone: staffData.phone,
                username: staffData.username,
                email: staffData.email,
                role: staffData.role,
                password: staffData.password,
                isActive: false,
                createdAt: new Date(),
                lastActivity: new Date()
            };
            
            const docRef = await db.collection('staff').add(staffDoc);
            console.log("Staff member added to Firestore:", docRef.id);
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
                lastActivity: new Date()
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

    // Function to load staff members from Firestore
    async function loadStaffMembers() {
        try {
            const staffList = document.getElementById('staffList');
            staffList.innerHTML = '<p>Loading staff members...</p>';
            
            const snapshot = await db.collection('staff').orderBy('createdAt', 'desc').get();
            
            if (snapshot.empty) {
                staffList.innerHTML = '<p>No staff members found.</p>';
                return;
            }
            
            let staffHTML = '';
            snapshot.forEach(doc => {
                const staff = doc.data();
                const isActive = staff.isActive || false;
                
                staffHTML += `
                    <div class="staff-card">
                        <div class="staff-avatar">
                            ${getRoleEmoji(staff.role)}
                        </div>
                        <div class="staff-info">
                            <div class="staff-name">${staff.name}</div>
                            <div class="staff-role ${staff.role}-badge">${staff.role.toUpperCase()}</div>
                            <div class="staff-details">
                                <div><i class="fas fa-user"></i> ${staff.username}</div>
                                <div><i class="fas fa-envelope"></i> ${staff.email}</div>
                                <div><i class="fas fa-phone"></i> ${staff.phone}</div>
                            </div>
                            <div class="staff-status-section">
                                <span class="online-indicator ${staff.isActive ? 'online' : 'offline'}"></span>
                                <span class="status-badge ${staff.isActive ? 'status-active' : 'status-offline'}">
                                    ${staff.isActive ? 'Active Now' : 'Offline'}
                                </span>
                                ${staff.lastActivity ? `<div class="last-activity">Last active: ${formatTimeAgo(staff.lastActivity.toDate())}</div>` : ''}
                            </div>
                        </div>
                        <div class="staff-actions">
                            <button class="edit-btn" onclick="openEditStaffModal('${doc.id}', '${staff.name}', '${staff.phone}', '${staff.email}', '${staff.username}', '${staff.role}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                    </div>
                `;
            });
            
            staffList.innerHTML = staffHTML;
            
        } catch (error) {
            console.error('Error loading staff members:', error);
            document.getElementById('staffList').innerHTML = '<p>Error loading staff members.</p>';
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

    // Function to open edit staff modal (updated to handle password)
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

    // Real-time staff status monitoring
    function startRealTimeStaffMonitoring() {
        if (staffUnsubscribe) {
            staffUnsubscribe(); // Clean up previous listener
        }
        
        staffUnsubscribe = db.collection('staff').onSnapshot((snapshot) => {
            console.log('Real-time staff update received');
            updateActiveStaffCount();
            if (document.getElementById('staff-section').style.display !== 'none') {
                loadStaffMembers();
            }
        }, (error) => {
            console.error('Error in real-time staff monitoring:', error);
        });
    }

    // Function to update active staff count in real-time
    async function updateActiveStaffCount() {
        try {
            const staffSnapshot = await db.collection('staff')
                .where('isActive', '==', true)
                .get();
            const activeStaffCount = staffSnapshot.size;
            document.getElementById('activeStaff').textContent = activeStaffCount;
        } catch (error) {
            console.error('Error updating active staff count:', error);
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
                    lastLogin: new Date(),
                    lastActivity: new Date()
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
                    lastLogout: new Date(),
                    lastActivity: new Date()
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

        // Staff form handling
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
                
                try {
                    await createStaffMember(staffData);
                    alert('Staff member added successfully!');
                    document.getElementById('addStaffForm').reset();
                    // No need to call loadStaffMembers() - real-time listener will handle it
                } catch (error) {
                    alert('Error adding staff member: ' + error.message);
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
            showSection('reports');
        });

        document.getElementById('totalOrders').closest('.stat-card').addEventListener('click', function() {
            showSection('reports');
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
        updateDateDisplay();
        
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
                            <option value="manager">Manager</option>
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