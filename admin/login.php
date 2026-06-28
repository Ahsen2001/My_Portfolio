<?php
session_start();
include '../config.php';

// Redirect if already logged in
if(isset($_SESSION['admin'])){
    header("Location: dashboard.php");
    exit();
}

if(isset($_POST['login'])){
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // PREPARED STATEMENT (SQL Injection Protection)
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $user = $result->fetch_assoc();

            // VERIFY HASHED PASSWORD
            if(password_verify($password, $user['password'])){
                // Prevent session fixation
                session_regenerate_id(true);
                $_SESSION['admin'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "User not found!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Portfolio</title>
    <!-- Use cohesive admin stylesheet -->
    <link rel="stylesheet" href="admin.css">
</head>
<body class="login-body">

<div class="login-container">
    <form method="POST" class="login-card">
        <h2>Admin <span>Login</span></h2>

        <?php if(isset($error)) { ?>
            <div class="toast error"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <input type="text" name="username" placeholder="Username" required class="form-input">
        <input type="password" name="password" placeholder="Password" required class="form-input">

        <button name="login" class="btn">Login</button>
        <div style="margin-top: 20px;">
            <a href="../index.php" style="color: var(--primary); text-decoration: none; font-size: 14px; font-weight: 500;">← Back to Site</a>
        </div>
    </form>
</div>

</body>
</html>