<?php
include '../config.php';

// Security Warning: Delete this file after creating the admin!
$success_msg = '';
$error_msg = '';

if (isset($_POST['create'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error_msg = "Username and password are required.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $username, $hash);
            if ($stmt->execute()) {
                $success_msg = "Admin account created successfully! Please DELETE this file (create_admin.php) immediately for security.";
            } else {
                $error_msg = "Failed to create admin. Username might already exist: " . $conn->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Database statement preparation failed: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Admin Account</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background: #1e293b;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            margin-top: 0;
            color: #38bdf8;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #94a3b8;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #475569;
            background: #0f172a;
            color: #fff;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #0ea5e9;
            border: none;
            color: #fff;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #0284c7;
        }
        .alert {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid #10b981;
            color: #34d399;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid #ef4444;
            color: #f87171;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Setup Admin Account</h2>
    <p style="font-size: 13px; color: #94a3b8; margin-bottom: 20px;">Use this utility to initialize or add an admin account directly to the database.</p>

    <?php if (!empty($success_msg)) { ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php } ?>

    <?php if (!empty($error_msg)) { ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" name="create">Create Admin Account</button>
    </form>
    
    <div style="margin-top: 20px; text-align: center;">
        <a href="login.php" style="color: #38bdf8; text-decoration: none; font-size: 14px;">Go to Login →</a>
    </div>
</div>

</body>
</html>
