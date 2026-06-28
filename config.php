<?php
// Secure Database Configuration
// Note: In production, consider loading these from environment variables.
$servername = "sql210.infinityfree.com";       // MySQL Hostname
$username = "if0_41585401";                     // MySQL Username
$password = "W6WApQSn1XpS";        // MySQL User password
$dbname = "if0_41585401_portfolio_db";         // MySQL Database name

// Gracefully connect with error reporting disabled for the user
mysqli_report(MYSQLI_REPORT_OFF);

$conn = @new mysqli($servername, $username, $password, $dbname);

// Check connection and output generic message to avoid credential leaks
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection error. Please try again later.");
}
?>