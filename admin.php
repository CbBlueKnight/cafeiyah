<?php
session_start();

// Demo accounts with hierarchical access
$valid_users = [
    'owner' => [
        'password' => 'owner123', 
        'role' => 'owner', 
        'name' => 'Cafe Owner',
        'permissions' => ['sales_reports', 'menu_management', 'staff_management', 'inventory_management', 'void_transactions', 'all_reports']
    ],
    'manager' => [
        'password' => 'manager123', 
        'role' => 'manager', 
        'name' => 'Cafe Manager',
        'permissions' => ['menu_management', 'staff_management', 'inventory_management', 'void_transactions']
    ],
    'cashier1' => [
        'password' => 'cashier123', 
        'role' => 'cashier', 
        'name' => 'Cashier 1',
        'permissions' => ['order_processing']
    ],
    'cashier2' => [
        'password' => 'cashier123', 
        'role' => 'cashier', 
        'name' => 'Cashier 2',
        'permissions' => ['order_processing']
    ]
];

// Handle Firebase Firestore login
if (isset($_POST['action']) && $_POST['action'] === 'firebase_login' && isset($_POST['user_data'])) {
    $user_data = json_decode($_POST['user_data'], true);
    
    if ($user_data && isset($user_data['id'])) {
        $_SESSION['user'] = [
            'name' => $user_data['name'],
            'role' => $user_data['role'],
            'username' => $user_data['username'],
            'email' => $user_data['email']
        ];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        $_SESSION['is_demo_account'] = false;
        $_SESSION['staff_id'] = $user_data['id'];
        
        // Redirect based on role
        switch ($user_data['role']) {
            case 'cashier':
                header('Location: cashier.php');
                exit;
            case 'manager':
                header('Location: manager.php');
                exit;
            case 'owner':
                header('Location: owner.php');
                exit;
            default:
                header('Location: admin.php');
                exit;
        }
    } else {
        $error = "Invalid login data";
    }
}

// Handle demo login
if (isset($_POST['action']) && $_POST['action'] === 'demo_login') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (isset($valid_users[$username]) && $valid_users[$username]['password'] === $password) {
        $_SESSION['user'] = $valid_users[$username];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        $_SESSION['is_demo_account'] = true;
        
        // Redirect based on role
        switch ($valid_users[$username]['role']) {
            case 'cashier':
                header('Location: cashier.php');
                exit;
            case 'manager':
                header('Location: manager.php');
                exit;
            case 'owner':
                header('Location: owner.php');
                exit;
            default:
                header('Location: admin.php');
                exit;
        }
    } else {
        $error = "Invalid username or password";
    }
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Check if user is logged in and redirect ONLY if we're not already processing a login
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
if ($logged_in && !isset($_POST['action'])) {
    switch ($_SESSION['user']['role']) {
        case 'cashier':
            header('Location: cashier.php');
            exit;
        case 'manager':
            header('Location: manager.php');
            exit;
        case 'owner':
            header('Location: owner.php');
            exit;
        default:
            // Stay on login page - no redirect
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Management System - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>
</head>
<body>
    <!-- Coffee Mug Splash Screen -->
    <div class="coffee-splash" id="coffeeSplash">
        <div class="splash-content">
            <div class="coffee-mug">
                <i class="fas fa-coffee"></i>
            </div>
            <div class="splash-text">
                <h1>Cafe Management System</h1>
                <p>Loading your coffee experience...</p>
            </div>
        </div>
    </div>

    <div class="login-container" id="loginContainer">
        <div class="login-box">
            <div class="logo">
                <i class="fas fa-coffee"></i>
                <h1>Cafe Management System</h1>
                <p>Hierarchical Access Portal</p>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Enter your username or email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                <div class="login-button-container">
                    <button type="submit" class="login-button">
                        Sign In <i class="fas fa-sign-in-alt"></i>
                    </button>
                </div>
            </form>
            
            <div id="loginError" class="error-message" style="display: none;">
                <i class="fas fa-exclamation-circle"></i> <span id="errorText"></span>
            </div>
            
            <div class="demo-accounts">
                <div class="demo-title">Demo Accounts:</div>
                <p><strong>👑 Owner:</strong> owner / owner123</p>
                <p><strong>💼 Manager:</strong> manager / manager123</p>
                <p><strong>🧾 Cashier 1:</strong> cashier1 / cashier123</p>
                <p><strong>🧾 Cashier 2:</strong> cashier2 / cashier123</p>
                <p style="margin-top: 10px; font-style: italic;">Or use your Firebase staff credentials</p>
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

        // Animation control using sessionStorage
        document.addEventListener('DOMContentLoaded', function() {
            const splash = document.getElementById('coffeeSplash');
            const loginContainer = document.getElementById('loginContainer');
            
            // Check if animation should be skipped
            const skipAnimation = sessionStorage.getItem('skipCoffeeAnimation');
            
            if (skipAnimation === 'true') {
                // Skip animation - remove splash immediately and show login
                if (splash) {
                    splash.remove();
                }
                if (loginContainer) {
                    loginContainer.style.opacity = '1';
                }
                // Clear the flag for future visits
                sessionStorage.removeItem('skipCoffeeAnimation');
            } else {
                // Show animation - set opacity after delay
                setTimeout(() => {
                    if (loginContainer) {
                        loginContainer.style.opacity = '1';
                    }
                }, 2500);
                
                // Remove splash after animation completes
                setTimeout(() => {
                    if (splash) {
                        splash.remove();
                    }
                }, 3000);
            }
        });

        // Demo accounts data
        const demoAccounts = {
            'owner': {pass: 'owner123', role: 'owner', name: 'Cafe Owner'},
            'manager': {pass: 'manager123', role: 'manager', name: 'Cafe Manager'},
            'cashier1': {pass: 'cashier123', role: 'cashier', name: 'Cashier 1'},
            'cashier2': {pass: 'cashier123', role: 'cashier', name: 'Cashier 2'}
        };

        // Function to handle Firestore authentication
        async function handleFirestoreLogin(username, password) {
            try {
                console.log("Attempting Firestore login for:", username);
                
                // Try to find user by username first
                let userQuery = await db.collection('staff')
                    .where('username', '==', username)
                    .limit(1)
                    .get();
                
                // If not found by username, try by email
                if (userQuery.empty && username.includes('@')) {
                    userQuery = await db.collection('staff')
                        .where('email', '==', username)
                        .limit(1)
                        .get();
                }
                
                if (userQuery.empty) {
                    throw new Error('Staff member not found');
                }
                
                const staffDoc = userQuery.docs[0];
                const staffData = staffDoc.data();
                
                console.log("Found staff data:", staffData);
                
                // Check password
                if (!staffData.password || staffData.password !== password) {
                    throw new Error('Invalid password');
                }
                
                // Update login status in Firestore
                await db.collection('staff').doc(staffDoc.id).update({
                    isActive: true,
                    lastLogin: new Date(),
                    lastActivity: new Date()
                });
                
                return {
                    id: staffDoc.id,
                    name: staffData.name,
                    email: staffData.email,
                    username: staffData.username,
                    role: staffData.role,
                    phone: staffData.phone
                };
                
            } catch (error) {
                console.error('Firestore login error:', error);
                throw error;
            }
        }

        // Function to submit demo login
        function submitDemoLogin(username, password) {
            // Set flag to skip animation on next visit
            sessionStorage.setItem('skipCoffeeAnimation', 'true');
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'admin.php';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'demo_login';
            form.appendChild(actionInput);
            
            const userInput = document.createElement('input');
            userInput.type = 'hidden';
            userInput.name = 'username';
            userInput.value = username;
            form.appendChild(userInput);
            
            const passInput = document.createElement('input');
            passInput.type = 'hidden';
            passInput.name = 'password';
            passInput.value = password;
            form.appendChild(passInput);
            
            document.body.appendChild(form);
            form.submit();
        }

        // Login form handling
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const usernameInput = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
            submitBtn.disabled = true;
            
            // Hide previous errors
            document.getElementById('loginError').style.display = 'none';
            
            try {
                // Check demo accounts first
                if (demoAccounts[usernameInput] && demoAccounts[usernameInput].pass === password) {
                    console.log('Demo account detected, logging in...');
                    submitDemoLogin(usernameInput, password);
                    return;
                }
                
                // Try Firebase login
                console.log('Attempting Firebase login...');
                const staffData = await handleFirestoreLogin(usernameInput, password);
                
                // Set flag to skip animation on next visit
                sessionStorage.setItem('skipCoffeeAnimation', 'true');
                
                // Create a form to submit staff login data to PHP
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'firebase_login';
                form.appendChild(actionInput);
                
                const userInput = document.createElement('input');
                userInput.type = 'hidden';
                userInput.name = 'user_data';
                userInput.value = JSON.stringify(staffData);
                form.appendChild(userInput);
                
                document.body.appendChild(form);
                form.submit();
                
            } catch (error) {
                // Show error message
                document.getElementById('errorText').textContent = error.message;
                document.getElementById('loginError').style.display = 'block';
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });

        // Add enter key support
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>
</html>