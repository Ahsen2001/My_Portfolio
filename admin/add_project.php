<?php
session_start();
include '../config.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['add'])){

    $title = trim($_POST['title']);
    $desc  = trim($_POST['description']);
    $link  = trim($_POST['github_link']);

    if(empty($title)){
        $error = "Title is required!";
    } else {

        $stmt = $conn->prepare("INSERT INTO projects (title, description, github_link) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $desc, $link);

        if($stmt->execute()){
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error adding project!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Project</title>

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

/* 🌙 Background Gradient */
body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
}

/* Glass Card */
.container{
    width:400px;
    padding:30px;
    border-radius:20px;
    backdrop-filter: blur(15px);
    background: rgba(255,255,255,0.05);
    border:1px solid rgba(255,255,255,0.1);
    box-shadow:0 8px 32px rgba(0,0,0,0.3);
    animation: fadeIn 0.8s ease;
}

h2{
    text-align:center;
    color:#fff;
    margin-bottom:20px;
}

/* Inputs */
input, textarea{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:none;
    border-radius:10px;
    outline:none;
    background: rgba(255,255,255,0.1);
    color:#fff;
}

input::placeholder, textarea::placeholder{
    color:#ccc;
}

/* Button */
button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#00c6ff;
    color:#fff;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#0072ff;
    transform:scale(1.05);
}

/* Error */
.error{
    color:#ff6b6b;
    text-align:center;
}

/* Animation */
@keyframes fadeIn{
    from{
        opacity:0;
        transform: translateY(20px);
    }
    to{
        opacity:1;
        transform: translateY(0);
    }
}

</style>
</head>

<body>

<div class="container">

    <h2>Add New Project</h2>

    <?php if(isset($error)){ ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <form method="POST">

        <input type="text" name="title" placeholder="Project Title" required>

        <textarea name="description" placeholder="Description"></textarea>

        <input type="url" name="github_link" placeholder="GitHub Link">

        <button name="add">Add Project</button>

    </form>

</div>

</body>
</html>