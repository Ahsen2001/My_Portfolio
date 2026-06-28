<?php
session_start();
include '../config.php';

// Protect page
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Validate ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid ID");
}

$id = $_GET['id'];

// Fetch project securely
$stmt = $conn->prepare("SELECT * FROM projects WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if(!$row){
    die("Project not found");
}

// Update project
if(isset($_POST['update'])){

    $title = trim($_POST['title']);
    $desc  = trim($_POST['description']);
    $link  = trim($_POST['github_link']);

    if(empty($title)){
        $error = "Title is required!";
    } else {

        $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, github_link=? WHERE id=?");
        $stmt->bind_param("sssi", $title, $desc, $link, $id);

        if($stmt->execute()){
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Update failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Project</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(135deg, #141e30, #243b55);
}

/* Glass Card */
.container{
    width:420px;
    padding:30px;
    border-radius:20px;
    backdrop-filter: blur(15px);
    background: rgba(255,255,255,0.05);
    border:1px solid rgba(255,255,255,0.1);
    box-shadow:0 8px 32px rgba(0,0,0,0.4);
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
    background:#ff7a18;
    color:#fff;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#ff3d00;
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

    <h2>✏️ Edit Project</h2>

    <?php if(isset($error)){ ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <form method="POST">

        <input type="text" name="title"
        value="<?php echo htmlspecialchars($row['title']); ?>" required>

        <textarea name="description"><?php echo htmlspecialchars($row['description']); ?></textarea>

        <input type="url" name="github_link"
        value="<?php echo htmlspecialchars($row['github_link']); ?>">

        <button name="update">Update Project</button>

    </form>

</div>

</body>
</html>