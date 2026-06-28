<?php
session_start();
include '../config.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Project count
$countResult = $conn->query("SELECT COUNT(*) as total FROM projects");
$countRow = $countResult->fetch_assoc();
$totalProjects = $countRow['total'];

// Get projects
$result = $conn->query("SELECT * FROM projects ORDER BY id DESC");

// CREATE ADMIN
if(isset($_POST['create_admin'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if(empty($username) || empty($password)){
        $admin_error = "All fields are required!";
    } else {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM admins WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){
            $admin_error = "Username already exists!";
        } else {

            $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashedPassword);

            if($stmt->execute()){
                $admin_success = "Admin created successfully!";
            } else {
                $admin_error = "Failed to create admin!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    display:flex;
    height:100vh;
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    color:#fff;
}

/* Sidebar */
.sidebar{
    width:220px;
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(15px);
    padding:20px;
    border-right:1px solid rgba(255,255,255,0.1);
}

.sidebar h2{
    text-align:center;
    margin-bottom:30px;
}

.sidebar a{
    display:block;
    padding:12px;
    margin:10px 0;
    border-radius:10px;
    text-decoration:none;
    color:#fff;
    transition:0.3s;
}

.sidebar a:hover{
    background:#00c6ff;
}

/* Main */
.main{
    flex:1;
    padding:30px;
    overflow:auto;
}

/* Topbar */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.search{
    padding:10px;
    border:none;
    border-radius:10px;
    outline:none;
    width:250px;
}

/* Stats */
.stats{
    display:flex;
    gap:20px;
    margin-bottom:20px;
}

.stat-card{
    flex:1;
    padding:20px;
    border-radius:15px;
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    text-align:center;
}

/* Cards */
.card{
    background: rgba(255,255,255,0.05);
    padding:20px;
    border-radius:15px;
    backdrop-filter: blur(10px);
    margin-bottom:20px;
    transition:0.3s;
}

.card:hover{
    transform: translateY(-5px);
}

.section-title{
    margin-bottom:10px;
    font-size:20px;
}

/* Table */
table{
    width:100%;
    border-collapse:collapse;
}

th, td{
    padding:12px;
}

th{
    border-bottom:1px solid rgba(255,255,255,0.2);
    text-align:left;
}

/* Buttons */
.edit{
    color:#00ffcc;
    margin-right:10px;
}

.delete{
    color:#ff6b6b;
}

/* Form */
.form-input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:none;
    border-radius:10px;
    outline:none;
    background: rgba(255,255,255,0.1);
    color:#fff;
}

.btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background: linear-gradient(135deg, #00c6ff, #0072ff);
    color:#fff;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.btn:hover{
    transform:scale(1.05);
}

/* Toast */
.toast{
    padding:10px;
    border-radius:8px;
    margin-bottom:10px;
}

.success{ background:#00c6ff; }
.error{ background:#ff6b6b; }

/* Animation */
.fade{
    animation: fadeIn 0.6s ease;
}

@keyframes fadeIn{
    from{opacity:0; transform:translateY(10px);}
    to{opacity:1; transform:translateY(0);}
}

</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>AHSEN</h2>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="add_project.php">➕ Add Project</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main -->
<div class="main fade">

    <div class="topbar">
        <h1>Dashboard</h1>
        <input type="text" class="search" placeholder="Search..." onkeyup="searchProject(this.value)">
    </div>

    <!-- Stats -->
    <div class="stats">
        <div class="stat-card">
            <h3><?php echo $totalProjects; ?></h3>
            <p>Total Projects</p>
        </div>
    </div>

    <!-- Projects -->
    <div class="card">
        <h2 class="section-title">📂 Project Management</h2>

        <table id="projectTable">
            <tr>
                <th>Title</th>
                <th>Actions</th>
            </tr>

            <?php while($row = $result->fetch_assoc()){ ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="edit">✏️ Edit</a>
                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="delete"
                       onclick="return confirm('Delete this project?')">🗑 Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Admin -->
    <div class="card">
        <h2 class="section-title">👤 Admin Management</h2>

        <?php if(isset($admin_error)){ ?>
            <div class="toast error"><?php echo $admin_error; ?></div>
        <?php } ?>

        <?php if(isset($admin_success)){ ?>
            <div class="toast success"><?php echo $admin_success; ?></div>
        <?php } ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required class="form-input">
            <input type="password" name="password" placeholder="Password" required class="form-input">
            <button name="create_admin" class="btn">➕ Create Admin</button>
        </form>
    </div>

</div>

<script>
function searchProject(input){
    let filter = input.toLowerCase();
    let rows = document.querySelectorAll("#projectTable tr");

    rows.forEach((row, index) => {
        if(index === 0) return;
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
}
</script>

</body>
</html>