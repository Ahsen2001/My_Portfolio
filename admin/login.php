<?php
session_start();
include '../config.php';

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // PREPARED STATEMENT (SQL Injection Protection)
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();

        // VERIFY HASHED PASSWORD
        if(password_verify($password, $user['password'])){
            $_SESSION['admin'] = $user['id'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }

    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<link rel="stylesheet" href="login.css">
</head>

<body>

<div class="login-container">

    <form method="POST" class="login-card">
        <h2>Admin Login</h2>

        <?php if(isset($error)) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <button name="login">Login</button>
    </form>

</div>

</body>
</html>