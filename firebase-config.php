<?php
// Firebase configuration with enhanced error handling
class FirebaseConfig {
    private $databaseUrl;
    private $apiKey;
    
    public function __construct() {
        // Replace with your Firebase project details
        $this->databaseUrl = 'https://cafe-iyah-5869e-default-rtdb.asia-southeast1.firebasedatabase.app/';
        $this->apiKey = 'AIzaSyCSRi9IyNkK6DA6YYfnAdzI9LigkgTVG24';
    }
    
    // GET request to Firebase with enhanced error handling
    public function getData($path) {
        $url = $this->databaseUrl . $path . '.json';
        
        // Create context with timeout
        $context = stream_context_create([
            'http' => [
                'timeout' => 10, // 10 second timeout
                'ignore_errors' => true // Don't fail on HTTP errors
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === FALSE) {
            $error = error_get_last();
            error_log("Firebase API Error - GET $path: " . $error['message']);
            return null;
        }
        
        $data = json_decode($response, true);
        
        // Check if data is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Firebase JSON Error - GET $path: " . json_last_error_msg());
            return null;
        }
        
        return $data;
    }
    
    // POST request to Firebase with retry logic
    public function postData($path, $data, $retries = 3) {
        $url = $this->databaseUrl . $path . '.json';
        
        for ($i = 0; $i < $retries; $i++) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false // For development, remove in production
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode === 200 && $response !== false) {
                $decodedResponse = json_decode($response, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decodedResponse;
                }
            }
            
            // Log error and wait before retry
            error_log("Firebase API Error - POST $path (Attempt " . ($i + 1) . "): HTTP $httpCode - $error");
            
            if ($i < $retries - 1) {
                usleep(500000 * ($i + 1)); // 0.5s, 1s, 1.5s
            }
        }
        
        error_log("Firebase API Error: Failed to post data to $path after $retries attempts");
        return false;
    }
    
    // PUT request for updating data
    public function putData($path, $data) {
        $url = $this->databaseUrl . $path . '.json';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 10
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        error_log("Firebase API Error - PUT $path: HTTP $httpCode");
        return false;
    }
    
    // DELETE request
    public function deleteData($path) {
        $url = $this->databaseUrl . $path . '.json';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_TIMEOUT => 10
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode === 200;
    }
    
    // Test Firebase connection
    public function testConnection() {
        $testData = $this->getData('');
        return $testData !== null;
    }
}

// Initialize session for cart with enhanced security
session_start();

// Regenerate session ID periodically for security
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Initialize Firebase
$firebase = new FirebaseConfig();

// Check Firebase connection (optional - for debugging)
if (isset($_GET['test_firebase'])) {
    if ($firebase->testConnection()) {
        echo "Firebase connection: SUCCESS";
    } else {
        echo "Firebase connection: FAILED";
    }
    exit();
}
?>