<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location - CAFÉ IYAH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #8B4513;
            --primary-dark: #654321;
            --primary-light: #a86a3d;
            --secondary-color: #28a745;
            --secondary-dark: #218838;
            --accent-color: #ff6b35;
            --text-color: #333;
            --text-light: #666;
            --background: #f8f5f2;
            --white: #ffffff;
            --border-color: #e8e2d9;
            --shadow: 0 4px 15px rgba(0,0,0,0.1);
            --shadow-hover: 0 6px 20px rgba(0,0,0,0.15);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--background);
            color: var(--text-color);
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--white);
            padding: 1.2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 15px rgba(139, 69, 19, 0.3);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        .logo {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 1px;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo a {
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.3s ease;
        }

        .logo a:hover {
            transform: translateY(-2px);
        }

        .logo-icon {
            font-size: 1.8rem;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .nav {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            position: relative;
        }

        .nav a {
            color: var(--white);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            position: relative;
            overflow: hidden;
        }

        .nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            transition: left 0.3s ease;
        }

        .nav a:hover::before {
            left: 0;
        }

        .nav a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,255,255,0.2);
        }

        .order-btn {
            background: var(--white);
            color: var(--primary-color);
            border: none;
            padding: 0.7rem 1.8rem;
            border-radius: 25px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .order-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255,255,255,0.4);
            background: var(--accent-color);
            color: var(--white);
        }

        .order-btn:active {
            transform: translateY(-1px);
        }

        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .location-container {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 2rem;
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            min-height: 600px;
            transition: transform 0.3s ease;
        }

        .location-container:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .map-section {
            position: relative;
            min-height: 600px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        #interactiveMap {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        .leaflet-container {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .map-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .map-btn {
            background: var(--white);
            border: none;
            border-radius: 15px;
            padding: 14px 20px;
            font-size: 0.95rem;
            cursor: pointer;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            font-weight: 600;
            min-width: 200px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .map-btn:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: translateY(-3px) scale(1.02);
            box-shadow: var(--shadow-hover);
        }

        .map-btn:active {
            transform: translateY(-1px);
        }

        .map-btn.primary {
            background: var(--primary-color);
            color: var(--white);
        }

        .map-btn.primary:hover {
            background: var(--primary-dark);
        }

        .map-btn.secondary {
            background: var(--secondary-color);
            color: var(--white);
        }

        .map-btn.secondary:hover {
            background: var(--secondary-dark);
        }

        .map-btn.accent {
            background: var(--accent-color);
            color: var(--white);
        }

        .map-btn.accent:hover {
            background: #e55a2b;
        }

        .map-overlay {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            z-index: 1000;
            max-width: 320px;
            border-left: 5px solid var(--primary-color);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .map-overlay h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .map-overlay p {
            margin: 8px 0;
            font-size: 0.95rem;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .loading {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.98);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 1001;
            text-align: center;
            border: 2px solid var(--primary-color);
            backdrop-filter: blur(10px);
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .location-status {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 18px 25px;
            border-radius: 12px;
            font-size: 0.95rem;
            z-index: 1000;
            display: none;
            box-shadow: var(--shadow);
            max-width: 350px;
            backdrop-filter: blur(10px);
            border-left: 4px solid var(--primary-color);
        }

        .location-status.success {
            background: rgba(212, 237, 218, 0.95);
            color: #155724;
            border-left-color: #28a745;
        }

        .location-status.error {
            background: rgba(248, 215, 218, 0.95);
            color: #721c24;
            border-left-color: #dc3545;
        }

        .location-status.info {
            background: rgba(209, 236, 241, 0.95);
            color: #0c5460;
            border-left-color: #17a2b8;
        }

        .info-section {
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(135deg, #f8f5f2 0%, #ffffff 100%);
        }

        .cafe-name {
            font-size: 2.5rem;
            color: var(--primary-color);
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cafe-name::after {
            content: '';
            flex: 1;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), transparent);
            border-radius: 2px;
        }

        .address {
            color: var(--text-light);
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            line-height: 1.6;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: rgba(139, 69, 19, 0.05);
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
        }

        .info-grid {
            display: grid;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            padding: 1rem;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid var(--border-color);
        }

        .info-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-light);
        }

        .info-item i {
            color: var(--primary-color);
            font-size: 1.4rem;
            width: 30px;
            text-align: center;
        }

        .info-item span {
            color: var(--text-color);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .hours {
            background: var(--white);
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2.5rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }

        .hours h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hours-table {
            width: 100%;
            font-size: 1rem;
        }

        .hours-table tr td {
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .hours-table tr:hover td {
            background: rgba(139, 69, 19, 0.05);
            transform: translateX(5px);
        }

        .hours-table tr:last-child td {
            border-bottom: none;
        }

        .hours-table tr td:last-child {
            text-align: right;
            color: var(--primary-color);
            font-weight: 700;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn {
            padding: 1.2rem 2rem;
            text-decoration: none;
            border-radius: 15px;
            font-weight: 700;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--primary-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-secondary:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .btn-accent {
            background: var(--accent-color);
            color: var(--white);
            grid-column: 1 / -1;
        }

        .btn-accent:hover {
            background: #e55a2b;
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        footer {
            text-align: center;
            padding: 2.5rem;
            color: var(--text-light);
            font-size: 0.95rem;
            margin-top: 3rem;
            background: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow);
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 90%;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.2);
            position: relative;
            animation: modalAppear 0.3s ease-out;
        }

        @keyframes modalAppear {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
        }

        .modal-btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .modal-btn.primary {
            background: var(--primary-color);
            color: var(--white);
        }

        .modal-btn.primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .modal-btn.secondary {
            background: var(--border-color);
            color: var(--text-color);
        }

        .modal-btn.secondary:hover {
            background: #d4c7b9;
            transform: translateY(-2px);
        }

        .location-options {
            display: grid;
            gap: 1.2rem;
            margin: 2rem 0;
        }

        .location-option {
            padding: 1.5rem;
            border: 2px solid var(--border-color);
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: left;
            background: var(--white);
        }

        .location-option:hover {
            border-color: var(--primary-color);
            background: rgba(139, 69, 19, 0.05);
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .location-option.selected {
            border-color: var(--primary-color);
            background: rgba(139, 69, 19, 0.1);
            box-shadow: var(--shadow);
        }

        .location-option h4 {
            color: var(--primary-color);
            margin-bottom: 0.8rem;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Search Styles */
        .search-container {
            position: relative;
            width: 100%;
            margin: 1.5rem 0;
        }

        .search-input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 1.3rem;
        }

        .suggestions-container {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--white);
            border: 2px solid var(--border-color);
            border-top: none;
            border-radius: 0 0 12px 12px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 1001;
            box-shadow: var(--shadow);
            display: none;
        }

        .suggestion-item {
            padding: 15px 20px;
            cursor: pointer;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.2s ease;
            text-align: left;
        }

        .suggestion-item:hover {
            background: rgba(139, 69, 19, 0.05);
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-main-text {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 5px;
        }

        .suggestion-secondary-text {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .suggestion-loading {
            padding: 15px 20px;
            text-align: center;
            color: var(--text-light);
            font-style: italic;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .location-container {
                grid-template-columns: 1fr;
            }
            
            .map-section {
                min-height: 400px;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }
            
            .container {
                padding: 0 1rem;
                margin: 1rem auto;
            }
            
            .info-section {
                padding: 2rem 1.5rem;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
            
            .map-controls {
                top: 10px;
                right: 10px;
            }
            
            .map-btn {
                padding: 12px 16px;
                font-size: 0.85rem;
                min-width: 180px;
            }
            
            .map-overlay {
                max-width: 280px;
                padding: 20px;
            }

            .modal-buttons {
                flex-direction: column;
            }
            
            .cafe-name {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .cafe-name {
                font-size: 1.8rem;
            }
            
            .info-section {
                padding: 1.5rem;
            }
            
            .btn {
                padding: 1rem 1.5rem;
            }
            
            .map-overlay {
                max-width: 250px;
                padding: 15px;
            }
            
            .map-btn {
                min-width: 160px;
                padding: 10px 14px;
            }
        }

        /* Custom Animations */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .bounce {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        /* Feature Highlights */
        .feature-highlight {
            position: relative;
        }

        .feature-highlight::after {
            content: 'NEW';
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--accent-color);
            color: var(--white);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 10px;
            animation: pulse 1.5s infinite;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <a href="index.php">
                <i class="fas fa-coffee logo-icon"></i>
                <span>CAFÉ IYAH</span>
            </a>
        </div>
        <nav class="nav">
            <a href="index.php">
                <i class="fas fa-home"></i>
                Home
            </a>
            <a href="menu.php">
                <button class="order-btn">
                    <i class="fas fa-utensils"></i>
                    Order Now
                </button>
            </a>
        </nav>
    </header>

    <div class="container">
        <div class="location-container">
            <div class="map-section">
                <div id="interactiveMap"></div>
                
                <div class="loading" id="loadingIndicator">
                    <div class="loading-spinner"></div>
                    <p><strong>Loading High-Quality Map</strong></p>
                    <p>Please wait while we prepare your navigation...</p>
                </div>
                
                <div class="location-status" id="locationStatus"></div>
                
                <div class="map-controls">
                    <button class="map-btn primary pulse" onclick="showDirectionsFromCurrentLocation()">
                        <i class="fas fa-location-crosshairs"></i>
                        Use My Location
                    </button>
                    <button class="map-btn secondary" onclick="showManualLocationModal()">
                        <i class="fas fa-map-marker-alt"></i>
                        Set Start Point
                    </button>
                    <button class="map-btn accent feature-highlight" onclick="shareLocation()">
                        <i class="fas fa-share-alt"></i>
                        Share Location
                    </button>
                    <button class="map-btn" onclick="resetMap()">
                        <i class="fas fa-sync-alt"></i>
                        Reset View
                    </button>
                </div>
                
                <div class="map-overlay">
                    <h3>
                        <i class="fas fa-mug-hot"></i>
                        ENGINEE'S CAFÉ
                    </h3>
                    <p>
                        <i class="fas fa-map-pin"></i>
                        <strong>Antipolo, Rizal, Philippines</strong>
                    </p>
                    <p>
                        <i class="fas fa-lightbulb"></i>
                        <strong>Pro Tip:</strong> Click "Set Start Point" for precise routing
                    </p>
                    <p>
                        <i class="fas fa-route"></i>
                        Multiple navigation options available
                    </p>
                </div>
            </div>
            
            <div class="info-section">
                <div class="cafe-name">
                    <i class="fas fa-star"></i>
                    ENGINEE'S CAFÉ
                </div>
                <div class="address">
                    <i class="fas fa-map-marker-alt"></i>
                    Prime location in Antipolo, Rizal - Your perfect coffee destination with stunning views and premium beverages
                </div>
                
                <div class="info-grid">
                    <div class="info-item" onclick="callPhone()">
                        <i class="fas fa-phone-alt"></i>
                        <span>+63 912 345 6789</span>
                    </div>
                    <div class="info-item" onclick="sendEmail()">
                        <i class="fas fa-envelope"></i>
                        <span>hello@engineerscafe.com</span>
                    </div>
                    <div class="info-item" onclick="openWaze()">
                        <i class="fab fa-waze"></i>
                        <span>Open in Waze</span>
                    </div>
                    <div class="info-item" onclick="saveLocation()">
                        <i class="fas fa-bookmark"></i>
                        <span>Save Location</span>
                    </div>
                </div>
                
                <div class="hours">
                    <h3>
                        <i class="fas fa-clock"></i>
                        OPENING HOURS
                    </h3>
                    <table class="hours-table">
                        <tr>
                            <td>Monday - Friday</td>
                            <td>6:00 AM - 10:00 PM</td>
                        </tr>
                        <tr>
                            <td>Saturday</td>
                            <td>7:00 AM - 11:00 PM</td>
                        </tr>
                        <tr>
                            <td>Sunday</td>
                            <td>7:00 AM - 9:00 PM</td>
                        </tr>
                        <tr>
                            <td><strong>Holidays</strong></td>
                            <td><strong>8:00 AM - 8:00 PM</strong></td>
                        </tr>
                    </table>
                </div>
                
                <div class="action-buttons">
                    <a href="https://maps.app.goo.gl/r8Ks6MajRvg25F1CA" class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        Open in Google Maps
                    </a>
                    <a href="tel:+639123456789" class="btn btn-secondary">
                        <i class="fas fa-phone"></i>
                        Call Now
                    </a>
                    <button class="btn btn-accent" onclick="showTransportOptions()">
                        <i class="fas fa-car"></i>
                        Get Transportation Options
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Location Modal -->
    <div class="modal-overlay" id="manualLocationModal">
        <div class="modal-content">
            <h3 class="modal-title">
                <i class="fas fa-map-marked-alt"></i>
                Set Starting Point
            </h3>
            <p>Choose how you'd like to set your journey starting location:</p>
            
            <div class="location-options">
                <div class="location-option selected" onclick="selectLocationOption('click')">
                    <h4><i class="fas fa-mouse-pointer"></i> Click on Map</h4>
                    <p>Precisely click anywhere on the map to set your exact starting location</p>
                </div>
                
                <div class="location-option" onclick="selectLocationOption('landmark')">
                    <h4><i class="fas fa-landmark"></i> Choose Landmark</h4>
                    <p>Select from popular nearby locations and landmarks</p>
                </div>
                
                <div class="location-option" onclick="selectLocationOption('address')">
                    <h4><i class="fas fa-search-location"></i> Enter Address</h4>
                    <p>Type your exact address for precise routing</p>
                </div>
            </div>
            
            <div class="modal-buttons">
                <button class="modal-btn primary" onclick="startManualLocationSelection()">
                    <i class="fas fa-play"></i>
                    Start Navigation
                </button>
                <button class="modal-btn secondary" onclick="closeManualLocationModal()">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Landmarks Modal -->
    <div class="modal-overlay" id="landmarksModal">
        <div class="modal-content">
            <h3 class="modal-title">
                <i class="fas fa-map-pin"></i>
                Choose Starting Point
            </h3>
            <p>Select a nearby landmark as your journey starting point:</p>
            
            <div class="location-options">
                <div class="location-option" onclick="selectLandmark('SM City Masinag', [14.6251, 121.1200])">
                    <h4><i class="fas fa-shopping-cart"></i> SM City Masinag</h4>
                    <p>Marcos Highway, Antipolo - Major shopping destination</p>
                </div>
                
                <div class="location-option" onclick="selectLandmark('Robinsons Place Antipolo', [14.5886, 121.1764])">
                    <h4><i class="fas fa-store"></i> Robinsons Place Antipolo</h4>
                    <p>Sumulong Highway, Antipolo - Shopping mall</p>
                </div>
                
                <div class="location-option" onclick="selectLandmark('Antipolo Cathedral', [14.5892, 121.1767])">
                    <h4><i class="fas fa-church"></i> Antipolo Cathedral</h4>
                    <p>P. Oliveros Street - Historical landmark</p>
                </div>
                
                <div class="location-option" onclick="selectLandmark('Lores Country Plaza', [14.6025, 121.1569])">
                    <h4><i class="fas fa-utensils"></i> Lores Country Plaza</h4>
                    <p>Marcos Highway - Dining and shopping complex</p>
                </div>
            </div>
            
            <div class="modal-buttons">
                <button class="modal-btn secondary" onclick="closeLandmarksModal()">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </button>
            </div>
        </div>
    </div>

    <!-- Address Modal -->
    <div class="modal-overlay" id="addressModal">
        <div class="modal-content">
            <h3 class="modal-title">
                <i class="fas fa-search-location"></i>
                Enter Your Location
            </h3>
            <p>Type your starting address or location name:</p>
            
            <div class="search-container">
                <input type="text" id="addressInput" 
                       placeholder="e.g., SM City Masinag, Marcos Highway, Antipolo" 
                       class="search-input">
                <div class="search-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="suggestions-container" id="suggestionsContainer"></div>
            </div>
            
            <div class="modal-buttons">
                <button class="modal-btn primary" onclick="searchAddress()">
                    <i class="fas fa-map-marked-alt"></i>
                    Set Location
                </button>
                <button class="modal-btn secondary" onclick="closeAddressModal()">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Transport Options Modal -->
    <div class="modal-overlay" id="transportModal">
        <div class="modal-content">
            <h3 class="modal-title">
                <i class="fas fa-car-side"></i>
                Transportation Options
            </h3>
            <p>Choose your preferred way to reach us:</p>
            
            <div class="location-options">
                <div class="location-option" onclick="openTransportApp('waze')">
                    <h4><i class="fab fa-waze"></i> Waze Navigation</h4>
                    <p>Real-time traffic and community-driven directions</p>
                </div>
                
                <div class="location-option" onclick="openTransportApp('google_maps')">
                    <h4><i class="fab fa-google"></i> Google Maps</h4>
                    <p>Comprehensive maps and public transport info</p>
                </div>
                
                <div class="location-option" onclick="openTransportApp('grab')">
                    <h4><i class="fas fa-taxi"></i> Grab / Ride-hailing</h4>
                    <p>Book a ride directly to our location</p>
                </div>
                
                <div class="location-option" onclick="openTransportApp('public')">
                    <h4><i class="fas fa-bus"></i> Public Transport</h4>
                    <p>Jeepney and bus routes information</p>
                </div>
            </div>
            
            <div class="modal-buttons">
                <button class="modal-btn secondary" onclick="closeTransportModal()">
                    <i class="fas fa-times"></i>
                    Close
                </button>
            </div>
        </div>
    </div>

    <footer>
        <p>
            <i class="fas fa-heart" style="color: #e25555;"></i>
            &copy; 2024 CAFÉ IYAH. Crafted with passion for coffee lovers.
            <i class="fas fa-coffee" style="color: var(--primary-color);"></i>
        </p>
        <p style="margin-top: 0.5rem; font-size: 0.85rem; opacity: 0.7;">
            <i class="fas fa-map-marker-alt"></i>
            Antipolo, Rizal | 
            <i class="fas fa-phone"></i>
            +63 912 345 6789 |
            <i class="fas fa-clock"></i>
            Open Daily
        </p>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Enhanced Map functionality with all features
        let map;
        let cafeMarker;
        let userMarker;
        let routeLayer;
        let clickHandler;
        let selectedLocationOption = 'click';
        let suggestionTimeout;
        
        const cafeLocation = [14.5703033, 121.1709399];
        
        // Enhanced initialization
        function initMap() {
            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.style.display = 'block';
            
            map = L.map('interactiveMap', {
                center: cafeLocation,
                zoom: 15,
                zoomControl: true,
                preferCanvas: true
            });

            // Multiple tile layers for better UX
            const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            const satelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0','mt1','mt2','mt3'],
                attribution: '© Google'
            });

            // Layer control
            const baseLayers = {
                "Street Map": osmLayer,
                "Satellite View": satelliteLayer
            };
            
            L.control.layers(baseLayers).addTo(map);
            
            // Enhanced cafe marker
            const cafeIcon = L.divIcon({
                html: `
                    <div style="
                        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
                        border: 3px solid white;
                        border-radius: 50%;
                        width: 60px;
                        height: 60px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                        font-size: 24px;
                        color: white;
                        animation: pulse 2s infinite;
                    ">🏠</div>
                `,
                className: 'cafe-marker',
                iconSize: [60, 60],
                iconAnchor: [30, 60],
                popupAnchor: [0, -60]
            });
            
            cafeMarker = L.marker(cafeLocation, { icon: cafeIcon })
                .addTo(map)
                .bindPopup(`
                    <div style="text-align: center; padding: 15px; min-width: 250px;">
                        <h3 style="margin: 0 0 10px 0; color: var(--primary-color); font-size: 1.3em;">
                            <i class="fas fa-mug-hot"></i> ENGINEE'S CAFÉ
                        </h3>
                        <p style="margin: 0 0 10px 0; color: var(--text-light);">
                            <i class="fas fa-map-marker-alt"></i> Antipolo, Rizal, Philippines
                        </p>
                        <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                            <button onclick="callPhone()" style="background: var(--primary-color); color: white; padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85em;">
                                <i class="fas fa-phone"></i> Call
                            </button>
                            <button onclick="showTransportOptions()" style="background: var(--secondary-color); color: white; padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85em;">
                                <i class="fas fa-directions"></i> Directions
                            </button>
                        </div>
                    </div>
                `)
                .openPopup();
            
            loadingIndicator.style.display = 'none';
            initAddressSearch();
        }

        // Enhanced GPS location function
        function showDirectionsFromCurrentLocation() {
            const loadingIndicator = document.getElementById('loadingIndicator');
            const locationStatus = document.getElementById('locationStatus');
            
            loadingIndicator.style.display = 'block';
            locationStatus.style.display = 'block';
            locationStatus.className = 'location-status info';
            locationStatus.innerHTML = '<i class="fas fa-satellite-dish"></i> Accessing GPS satellite...';
            
            if (!navigator.geolocation) {
                showManualLocationModal();
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                position => {
                    const userLocation = [position.coords.latitude, position.coords.longitude];
                    showLocationSuccess(userLocation, 'GPS Location');
                },
                error => {
                    handleLocationError(error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 60000
                }
            );
        }

        function showLocationSuccess(userLocation, source) {
            const locationStatus = document.getElementById('locationStatus');
            locationStatus.className = 'location-status success';
            locationStatus.innerHTML = `<i class="fas fa-check-circle"></i> ${source} acquired successfully!`;
            
            setUserLocation(userLocation, `Your ${source}`);
            calculateAndDisplayRoute(userLocation, cafeLocation);
            
            document.getElementById('loadingIndicator').style.display = 'none';
        }

        function handleLocationError(error) {
            const locationStatus = document.getElementById('locationStatus');
            locationStatus.className = 'location-status error';
            
            const errors = {
                1: 'Location access denied. Please enable location permissions.',
                2: 'Location unavailable. Please check your connection.',
                3: 'Location request timeout. Please try again.',
                0: 'Unable to determine your location.'
            };
            
            locationStatus.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${errors[error.code] || errors[0]}`;
            document.getElementById('loadingIndicator').style.display = 'none';
            
            setTimeout(() => showManualLocationModal(), 2000);
        }

        // Enhanced modal functions
        function showManualLocationModal() {
            document.getElementById('manualLocationModal').style.display = 'flex';
        }

        function closeManualLocationModal() {
            document.getElementById('manualLocationModal').style.display = 'none';
        }

        function selectLocationOption(option) {
            selectedLocationOption = option;
            document.querySelectorAll('.location-option').forEach(el => {
                el.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
        }

        function startManualLocationSelection() {
            closeManualLocationModal();
            const locationStatus = document.getElementById('locationStatus');
            locationStatus.style.display = 'block';
            locationStatus.className = 'location-status info';
            
            switch(selectedLocationOption) {
                case 'click':
                    locationStatus.innerHTML = '<i class="fas fa-mouse-pointer"></i> Click anywhere on the map to set your starting point';
                    enableMapClickSelection();
                    break;
                case 'landmark':
                    showLandmarksModal();
                    break;
                case 'address':
                    showAddressModal();
                    break;
            }
        }

        function enableMapClickSelection() {
            if (clickHandler) map.off('click', clickHandler);
            
            clickHandler = e => {
                const userLocation = [e.latlng.lat, e.latlng.lng];
                setUserLocation(userLocation, 'Selected Location');
                calculateAndDisplayRoute(userLocation, cafeLocation);
                
                const locationStatus = document.getElementById('locationStatus');
                locationStatus.className = 'location-status success';
                locationStatus.innerHTML = '<i class="fas fa-check-circle"></i> Starting point set! Route calculated.';
                
                map.off('click', clickHandler);
                clickHandler = null;
            };
            
            map.on('click', clickHandler);
        }

        // Landmark functions
        function showLandmarksModal() {
            document.getElementById('landmarksModal').style.display = 'flex';
        }

        function closeLandmarksModal() {
            document.getElementById('landmarksModal').style.display = 'none';
        }

        function selectLandmark(name, coordinates) {
            setUserLocation(coordinates, name);
            calculateAndDisplayRoute(coordinates, cafeLocation);
            showStatus(`<i class="fas fa-map-marker-alt"></i> Starting from ${name}`);
            closeLandmarksModal();
        }

        // Address search functions
        function showAddressModal() {
            document.getElementById('addressModal').style.display = 'flex';
            setTimeout(() => {
                document.getElementById('addressInput').focus();
            }, 300);
        }

        function closeAddressModal() {
            document.getElementById('addressModal').style.display = 'none';
            hideSuggestions();
        }

        function initAddressSearch() {
            const addressInput = document.getElementById('addressInput');
            if (!addressInput) return;
            
            addressInput.addEventListener('input', e => {
                clearTimeout(suggestionTimeout);
                const query = e.target.value.trim();
                
                if (query.length < 2) {
                    hideSuggestions();
                    return;
                }
                
                suggestionTimeout = setTimeout(() => searchAddressSuggestions(query), 500);
            });
            
            addressInput.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchAddress();
                } else if (e.key === 'Escape') {
                    hideSuggestions();
                }
            });
            
            document.addEventListener('click', e => {
                if (!e.target.closest('.search-container')) {
                    hideSuggestions();
                }
            });
        }

        function searchAddressSuggestions(query) {
            const suggestionsContainer = document.getElementById('suggestionsContainer');
            suggestionsContainer.innerHTML = '<div class="suggestion-loading"><i class="fas fa-spinner fa-spin"></i> Searching locations...</div>';
            suggestionsContainer.style.display = 'block';
            
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=ph`)
            .then(response => response.json())
            .then(data => {
                suggestionsContainer.innerHTML = '';
                if (data && data.length > 0) {
                    data.forEach(location => {
                        const suggestionItem = document.createElement('div');
                        suggestionItem.className = 'suggestion-item';
                        suggestionItem.innerHTML = `
                            <div class="suggestion-main-text">${location.display_name}</div>
                        `;
                        suggestionItem.addEventListener('click', () => selectAddressSuggestion(location));
                        suggestionsContainer.appendChild(suggestionItem);
                    });
                } else {
                    suggestionsContainer.innerHTML = '<div class="suggestion-loading">No locations found</div>';
                }
            })
            .catch(error => {
                suggestionsContainer.innerHTML = '<div class="suggestion-loading">Error searching locations</div>';
            });
        }

        function selectAddressSuggestion(location) {
            document.getElementById('addressInput').value = location.display_name;
            hideSuggestions();
            
            const userLocation = [parseFloat(location.lat), parseFloat(location.lon)];
            setUserLocation(userLocation, location.display_name);
            calculateAndDisplayRoute(userLocation, cafeLocation);
            showStatus(`<i class="fas fa-check-circle"></i> Location set: ${location.display_name}`);
            closeAddressModal();
        }

        function searchAddress() {
            const query = document.getElementById('addressInput').value.trim();
            if (!query) {
                showAlert('Please enter an address or location');
                return;
            }
            
            const loadingIndicator = document.getElementById('loadingIndicator');
            loadingIndicator.style.display = 'block';
            hideSuggestions();
            
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=ph`)
            .then(response => response.json())
            .then(data => {
                loadingIndicator.style.display = 'none';
                if (data && data.length > 0) {
                    const result = data[0];
                    const userLocation = [parseFloat(result.lat), parseFloat(result.lon)];
                    setUserLocation(userLocation, result.display_name);
                    calculateAndDisplayRoute(userLocation, cafeLocation);
                    showStatus(`<i class="fas fa-check-circle"></i> Location found: ${result.display_name}`);
                    closeAddressModal();
                } else {
                    showAlert('Address not found. Please try a different location.');
                }
            })
            .catch(error => {
                loadingIndicator.style.display = 'none';
                showAlert('Error searching address. Please try again.');
            });
        }

        // Enhanced utility functions
        function setUserLocation(coordinates, popupText) {
            if (userMarker) map.removeLayer(userMarker);
            
            const userIcon = L.divIcon({
                html: `
                    <div style="
                        background: linear-gradient(135deg, var(--secondary-color), var(--secondary-dark));
                        border: 3px solid white;
                        border-radius: 50%;
                        width: 45px;
                        height: 45px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                        font-size: 18px;
                        color: white;
                    ">📍</div>
                `,
                className: 'user-marker',
                iconSize: [45, 45],
                iconAnchor: [22, 45]
            });
            
            userMarker = L.marker(coordinates, { icon: userIcon })
                .addTo(map)
                .bindPopup(popupText)
                .openPopup();
            
            const bounds = L.latLngBounds([coordinates, cafeLocation]);
            map.fitBounds(bounds, { padding: [60, 60] });
        }

        function calculateAndDisplayRoute(start, end) {
            if (routeLayer) map.removeLayer(routeLayer);
            
            showStatus('<i class="fas fa-route"></i> Calculating optimal route...');
            
            fetch(`https://router.project-osrm.org/route/v1/driving/${start[1]},${start[0]};${end[1]},${end[0]}?overview=full&geometries=geojson`)
            .then(response => response.json())
            .then(data => {
                if (data.routes && data.routes.length > 0) {
                    const route = data.routes[0];
                    const routeCoordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
                    
                    routeLayer = L.polyline(routeCoordinates, {
                        color: '#8B4513',
                        weight: 6,
                        opacity: 0.9,
                        lineJoin: 'round'
                    }).addTo(map);
                    
                    const distance = (route.distance / 1000).toFixed(1);
                    const duration = Math.ceil(route.duration / 60);
                    showStatus(`<i class="fas fa-check-circle"></i> Route found! ${distance} km • ${duration} min drive`);
                }
            })
            .catch(error => {
                routeLayer = L.polyline([start, end], {
                    color: '#8B4513',
                    weight: 4,
                    opacity: 0.6,
                    dashArray: '10, 10'
                }).addTo(map);
                showStatus('<i class="fas fa-info-circle"></i> Direct route shown');
            });
        }

        // New feature functions
        function shareLocation() {
            if (navigator.share) {
                navigator.share({
                    title: 'ENGINEE\'S CAFÉ Location',
                    text: 'Check out this amazing café!',
                    url: 'https://maps.app.goo.gl/r8Ks6MajRvg25F1CA'
                });
            } else {
                navigator.clipboard.writeText('https://maps.app.goo.gl/r8Ks6MajRvg25F1CA').then(() => {
                    showAlert('Location link copied to clipboard!');
                });
            }
        }

        function showTransportOptions() {
            document.getElementById('transportModal').style.display = 'flex';
        }

        function closeTransportModal() {
            document.getElementById('transportModal').style.display = 'none';
        }

        function openTransportApp(app) {
            const urls = {
                'waze': 'https://waze.com/ul?ll=14.5703033,121.1709399&navigate=yes',
                'google_maps': 'https://maps.google.com/maps?daddr=14.5703033,121.1709399',
                'grab': 'grab://',
                'public': 'https://maps.google.com/maps?daddr=14.5703033,121.1709399&dirflg=r'
            };
            
            if (urls[app]) {
                window.open(urls[app], '_blank');
            }
            closeTransportModal();
        }

        function callPhone() {
            window.open('tel:+639123456789', '_self');
        }

        function sendEmail() {
            window.open('mailto:hello@engineerscafe.com?subject=Inquiry%20about%20ENGINEE%27S%20CAF%C3%89&body=Hello%2C%20I%20would%20like%20to%20know%20more%20about...', '_self');
        }

        function openWaze() {
            window.open('https://waze.com/ul?ll=14.5703033,121.1709399&navigate=yes', '_blank');
        }

        function saveLocation() {
            if (navigator.share) {
                navigator.share({
                    title: 'ENGINEE\'S CAFÉ - Saved Location',
                    text: 'Cafe location saved for future reference',
                    url: window.location.href
                });
            } else {
                // Fallback: Add to localStorage
                const savedLocations = JSON.parse(localStorage.getItem('savedLocations') || '[]');
                savedLocations.push({
                    name: 'ENGINEE\'S CAFÉ',
                    address: 'Antipolo, Rizal',
                    coordinates: cafeLocation,
                    timestamp: new Date().toISOString()
                });
                localStorage.setItem('savedLocations', JSON.stringify(savedLocations));
                showAlert('Location saved to your browser!');
            }
        }

        function resetMap() {
            if (userMarker) {
                map.removeLayer(userMarker);
                userMarker = null;
            }
            if (routeLayer) {
                map.removeLayer(routeLayer);
                routeLayer = null;
            }
            if (clickHandler) {
                map.off('click', clickHandler);
                clickHandler = null;
            }
            
            map.setView(cafeLocation, 15);
            cafeMarker.openPopup();
            
            const locationStatus = document.getElementById('locationStatus');
            locationStatus.style.display = 'none';
        }

        function hideSuggestions() {
            const suggestionsContainer = document.getElementById('suggestionsContainer');
            if (suggestionsContainer) {
                suggestionsContainer.style.display = 'none';
            }
        }

        function showStatus(message) {
            const locationStatus = document.getElementById('locationStatus');
            locationStatus.style.display = 'block';
            locationStatus.innerHTML = message;
        }

        function showAlert(message) {
            alert(message);
        }

        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (map) {
                setTimeout(() => {
                    map.invalidateSize();
                }, 100);
            }
        });
    </script>
</body>
</html>