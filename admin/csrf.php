<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate a secure random token if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Get the active CSRF token.
 */
function get_csrf_token() {
    return $_SESSION['csrf_token'];
}

/**
 * Generate a hidden input element containing the CSRF token.
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(get_csrf_token()) . '">';
}

/**
 * Verify if the submitted token matches the session token.
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Helper to validate POST requests directly.
 */
function validate_csrf_post_or_die() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!verify_csrf_token($token)) {
            http_response_code(403);
            die("CSRF validation failed. Unauthorized request.");
        }
    }
}
?>
