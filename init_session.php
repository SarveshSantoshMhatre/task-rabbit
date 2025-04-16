<?php
// Only initialize session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    // Set session cookie parameters
    $lifetime = 7 * 24 * 60 * 60; // 7 days
    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    // Start the session
    session_start();
    
    // Set last activity time if not set
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    }
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug session information
error_log("=== Session Initialized ===");
error_log("Session ID: " . session_id());
error_log("Session Data: " . print_r($_SESSION, true));
error_log("Cookie Data: " . print_r($_COOKIE, true));
?> 