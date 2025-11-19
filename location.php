<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location - CAFÉ IYAH</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f8f5f2;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background: #8B4513;
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(139, 69, 19, 0.3);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .logo a {
            color: white;
            text-decoration: none;
        }

        .nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }

        .nav a:hover {
            opacity: 0.8;
        }

        .order-btn {
            background: white;
            color: #8B4513;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .order-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .location-container {
            display: flex;
            gap: 2rem;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            min-height: 500px;
        }

        .map-section {
            flex: 1.2;
            min-height: 500px;
            position: relative;
        }

        #interactiveMap {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        .map-controls {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .map-btn {
            background: white;
            border: none;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.9rem;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .map-btn:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }

        .map-btn.primary {
            background: #8B4513;
            color: white;
        }

        .map-btn.primary:hover {
            background: #654321;
        }

        .map-overlay {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            max-width: 250px;
            border-left: 4px solid #8B4513;
        }

        .map-overlay h3 {
            color: #8B4513;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        .map-overlay p {
            font-size: 0.9rem;
            margin-bottom: 5px;
            color: #666;
        }

        .loading {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 1001;
            text-align: center;
            border: 2px solid #8B4513;
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #8B4513;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .location-status {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 0.85rem;
            z-index: 1000;
            display: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            max-width: 300px;
        }

        .location-status.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .location-status.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .info-section {
            flex: 0.8;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cafe-name {
            font-size: 2rem;
            color: #8B4513;
            font-weight: bold;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .address {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .info-grid {
            display: grid;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 0;
        }

        .info-item i {
            color: #8B4513;
            font-size: 1.2rem;
            width: 24px;
        }

        .info-item span {
            color: #555;
            font-weight: 500;
        }

        .hours {
            background: #f8f5f2;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .hours h3 {
            color: #8B4513;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .hours-table {
            width: 100%;
            font-size: 0.95rem;
        }

        .hours-table tr td {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e8e2d9;
        }

        .hours-table tr:last-child td {
            border-bottom: none;
        }

        .hours-table tr td:last-child {
            text-align: right;
            color: #8B4513;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            text-align: center;
            flex: 1;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: #8B4513;
            color: white;
        }

        .btn-primary:hover {
            background: #654321;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #8B4513;
            border: 2px solid #8B4513;
        }

        .btn-secondary:hover {
            background: #8B4513;
            color: white;
            transform: translateY(-2px);
        }

        footer {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-size: 0.9rem;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .location-container {
                flex-direction: column;
            }
            
            .map-section {
                min-height: 400px;
            }
            
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
                padding: 2rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .map-controls {
                top: 10px;
                right: 10px;
            }
            
            .map-btn {
                padding: 10px 14px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .cafe-name {
                font-size: 1.5rem;
            }
            
            .info-section {
                padding: 1.5rem;
            }
            
            .btn {
                padding: 0.875rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <a href="index.php">café iyah</a>
        </div>
        <nav class="nav">
            <a href="index.php">Home</a>
            <a href="menu.php"><button class="order-btn">Order Now</button></a>
        </nav>
    </header>

    <div class="container">
        <div class="location-container">
            <div class="map-section">
                <!-- Interactive Map Container -->
                <div id="interactiveMap"></div>
                
                <!-- Loading Indicator -->
                <div class="loading" id="loadingIndicator">
                    <div class="loading-spinner"></div>
                    <p><strong>Loading Map</strong></p>
                    <p>Please wait...</p>
                </div>
                
                <!-- Location Status -->
                <div class="location-status" id="locationStatus"></div>
                
                <!-- Map Controls -->
                <div class="map-controls">
                    <button class="map-btn primary" onclick="showDirectionsFromCurrentLocation()">
                        📍 Show Route from My Location
                    </button>
                    <button class="map-btn" onclick="resetMap()">
                        🗺️ Reset View
                    </button>
                </div>
                
                <!-- Map Overlay -->
                <div class="map-overlay">
                    <h3>ENGINEE'S CAFÉ</h3>
                    <p>📍 Antipolo, Rizal</p>
                    <p>Click buttons to get directions</p>
                </div>
            </div>
            
            <div class="info-section">
                <div class="cafe-name">ENGINEE'S CAFÉ</div>
                <div class="address">Antipolo, Rizal, Philippines</div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <i>📞</i>
                        <span>+63 912 345 6789</span>
                    </div>
                    <div class="info-item">
                        <i>✉️</i>
                        <span>hello@engineerscafe.com</span>
                    </div>
                </div>
                
                <div class="hours">
                    <h3>OPENING HOURS</h3>
                    <table class="hours-table">
                        <tr>
                            <td>Monday - Friday</td>
                            <td>6AM - 10PM</td>
                        </tr>
                        <tr>
                            <td>Saturday</td>
                            <td>7AM - 11PM</td>
                        </tr>
                        <tr>
                            <td>Sunday</td>
                            <td>7AM - 9PM</td>
                        </tr>
                    </table>
                </div>
                
                <div class="action-buttons">
                    <a href="https://maps.app.goo.gl/r8Ks6MajRvg25F1CA" class="btn btn-primary" target="_blank">Open in Google Maps</a>
                    <a href="tel:+639123456789" class="btn btn-secondary">Call Us</a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> CAFÉ IYAH. All rights reserved.</p>
    </footer>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Map variables
        let map;
        let cafeMarker;
        let userMarker;
        let routeLayer;
        
        // Cafe coordinates (ENGINEE'S CAFÉ in Antipolo, Rizal)
        const cafeLocation = [14.5703033, 121.1709399];
        
        // Initialize the map
        function initMap() {
            const loadingIndicator = document.getElementById('loadingIndicator');
            
            // Show loading
            loadingIndicator.style.display = 'block';
            
            // Initialize the map
            map = L.map('interactiveMap').setView(cafeLocation, 15);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Add cafe marker with custom icon
            const cafeIcon = L.divIcon({
                html: '☕',
                className: 'cafe-marker',
                iconSize: [40, 40],
                iconAnchor: [20, 40]
            });
            
            cafeMarker = L.marker(cafeLocation, { icon: cafeIcon })
                .addTo(map)
                .bindPopup(`
                    <div style="text-align: center; padding: 10px;">
                        <h3 style="margin: 0 0 8px 0; color: #8B4513;">ENGINEE'S CAFÉ</h3>
                        <p style="margin: 0 0 8px 0;">Antipolo, Rizal, Philippines</p>
                        <a href="https://maps.app.goo.gl/r8Ks6MajRvg25F1CA" target="_blank" 
                           style="color: #8B4513; text-decoration: none; font-weight: bold;">
                            Get Directions →
                        </a>
                    </div>
                `)
                .openPopup();
            
            // Hide loading
            loadingIndicator.style.display = 'none';
        }
        
        // Show directions from current location
        function showDirectionsFromCurrentLocation() {
            const loadingIndicator = document.getElementById('loadingIndicator');
            const locationStatus = document.getElementById('locationStatus');
            
            // Show loading
            loadingIndicator.style.display = 'block';
            locationStatus.style.display = 'block';
            locationStatus.className = 'location-status';
            locationStatus.innerHTML = '🔍 Detecting your current location...';
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        const userLocation = [userLat, userLng];
                        
                        // Update status
                        locationStatus.className = 'location-status success';
                        locationStatus.innerHTML = '✅ Location found! Calculating route...';
                        
                        // Add user location marker
                        if (userMarker) {
                            map.removeLayer(userMarker);
                        }
                        
                        const userIcon = L.divIcon({
                            html: '📍',
                            className: 'user-marker',
                            iconSize: [30, 30],
                            iconAnchor: [15, 30]
                        });
                        
                        userMarker = L.marker(userLocation, { icon: userIcon })
                            .addTo(map)
                            .bindPopup('Your Current Location')
                            .openPopup();
                        
                        // Calculate and display route
                        calculateAndDisplayRoute(userLocation, cafeLocation);
                        
                        // Fit map to show both locations
                        const bounds = L.latLngBounds([userLocation, cafeLocation]);
                        map.fitBounds(bounds, { padding: [20, 20] });
                        
                        // Hide loading
                        loadingIndicator.style.display = 'none';
                        
                        // Hide status after 5 seconds
                        setTimeout(() => {
                            locationStatus.style.display = 'none';
                        }, 5000);
                    },
                    function(error) {
                        // Handle errors
                        loadingIndicator.style.display = 'none';
                        locationStatus.style.display = 'block';
                        locationStatus.className = 'location-status error';
                        
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                locationStatus.innerHTML = '❌ Location access denied. Please allow location access.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                locationStatus.innerHTML = '❌ Location information unavailable.';
                                break;
                            case error.TIMEOUT:
                                locationStatus.innerHTML = '⏰ Location request timed out.';
                                break;
                            default:
                                locationStatus.innerHTML = '❌ An unknown error occurred.';
                                break;
                        }
                        
                        // Fallback: Open Google Maps
                        setTimeout(() => {
                            const confirmFallback = confirm('Unable to get your location. Would you like to open Google Maps for directions?');
                            if (confirmFallback) {
                                window.open('https://www.google.com/maps/dir//ENGINEE\'S+CAFÉ+Antipolo+Rizal/', '_blank');
                            }
                        }, 1000);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 60000
                    }
                );
            } else {
                // Geolocation not supported
                loadingIndicator.style.display = 'none';
                locationStatus.style.display = 'block';
                locationStatus.className = 'location-status error';
                locationStatus.innerHTML = '❌ Geolocation is not supported by your browser.';
                
                setTimeout(() => {
                    locationStatus.style.display = 'none';
                }, 5000);
            }
        }
        
        // Calculate and display route using OSRM API
        function calculateAndDisplayRoute(start, end) {
            // Remove existing route if any
            if (routeLayer) {
                map.removeLayer(routeLayer);
            }
            
            // Use OSRM API for routing
            const url = `https://router.project-osrm.org/route/v1/driving/${start[1]},${start[0]};${end[1]},${end[0]}?overview=full&geometries=geojson`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.routes && data.routes.length > 0) {
                        const route = data.routes[0];
                        const routeCoordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
                        
                        // Create route line
                        routeLayer = L.polyline(routeCoordinates, {
                            color: '#8B4513',
                            weight: 6,
                            opacity: 0.8,
                            lineJoin: 'round'
                        }).addTo(map);
                        
                        // Update status with route info
                        const distance = (route.distance / 1000).toFixed(1);
                        const duration = Math.ceil(route.duration / 60);
                        const locationStatus = document.getElementById('locationStatus');
                        locationStatus.innerHTML = `✅ Route found! ${distance} km • ${duration} min drive`;
                    }
                })
                .catch(error => {
                    console.error('Routing error:', error);
                    // Fallback: Draw straight line if routing fails
                    routeLayer = L.polyline([start, end], {
                        color: '#8B4513',
                        weight: 4,
                        opacity: 0.6,
                        dashArray: '10, 10'
                    }).addTo(map);
                    
                    const locationStatus = document.getElementById('locationStatus');
                    locationStatus.innerHTML = '⚠️ Direct route shown (detailed routing unavailable)';
                });
        }
        
        // Reset map to default view
        function resetMap() {
            if (userMarker) {
                map.removeLayer(userMarker);
                userMarker = null;
            }
            if (routeLayer) {
                map.removeLayer(routeLayer);
                routeLayer = null;
            }
            
            map.setView(cafeLocation, 15);
            cafeMarker.openPopup();
            
            const locationStatus = document.getElementById('locationStatus');
            locationStatus.style.display = 'none';
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