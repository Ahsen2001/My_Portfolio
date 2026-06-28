<?php
// Secure Database Configuration
// Automatically detects environment (Localhost vs InfinityFree Production)
$is_local = false;
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
    if ($host === 'localhost' || $host === '127.0.0.1' || strpos($host, 'localhost:') === 0 || strpos($host, '127.0.0.1:') === 0) {
        $is_local = true;
    }
} else {
    // Fallback for command line execution
    $is_local = true;
}

if ($is_local) {
    $servername = "127.0.0.1";
    $username = "root";
    $password = "";
    $dbname = "portfolio";
} else {
    $servername = "sql210.infinityfree.com";
    $username = "if0_41585401";
    $password = "W6WApQSn1XpS";
    $dbname = "if0_41585401_portfolio_db";
}

// Gracefully connect with error reporting disabled for the user
mysqli_report(MYSQLI_REPORT_OFF);

$conn = @new mysqli($servername, $username, $password, $dbname);

// Check connection and output generic message to avoid credential leaks
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection error. Please try again later.");
}
?>