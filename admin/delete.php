<?php
session_start();
include '../config.php';
include 'csrf.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID");
}

// Validate CSRF token for DELETE action (GET request)
if (!isset($_GET['csrf_token']) || !verify_csrf_token($_GET['csrf_token'])) {
    die("CSRF token validation failed. Unauthorized request.");
}

$id = intval($_GET['id']);

// PREPARED STATEMENT
$stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: dashboard.php");
exit();