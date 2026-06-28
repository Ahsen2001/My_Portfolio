<?php
session_start();
include '../config.php';
include 'csrf.php';

// Authentication Guard
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Validate ID parameter
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid ID");
}

$id = intval($_GET['id']);

// Fetch project securely
$stmt = $conn->prepare("SELECT * FROM projects WHERE id=?");
if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
} else {
    die("Database query failed.");
}

if(!$row){
    die("Project not found");
}

// Update project
if(isset($_POST['update'])){
    // CSRF check
    validate_csrf_post_or_die();

    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $link  = trim($_POST['github_link'] ?? '');

    if(empty($title)){
        $error = "Title is required!";
    } else {
        // Validate URL format if provided
        if (!empty($link) && !filter_var($link, FILTER_VALIDATE_URL)) {
            $error = "Invalid GitHub link format! Must be a valid URL starting with http:// or https://";
        } else {
            $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, github_link=? WHERE id=?");
            if ($stmt) {
                $stmt->bind_param("sssi", $title, $desc, $link, $id);
                if($stmt->execute()){
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Update execution failed. Please try again.";
                }
                $stmt->close();
            } else {
                $error = "Failed to prepare database update statement.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project | Portfolio Manager</title>
    <!-- Use unified admin stylesheet -->
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
    <h2>AHSEN <span>PORTFOLIO</span></h2>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="add_project.php">➕ Add Project</a>
    <a href="../index.php" target="_blank">🌐 View Site</a>
    <a href="logout.php" style="margin-top: auto; color: var(--danger);">🚪 Logout</a>
</div>

<!-- Main Area -->
<div class="main fade">
    <div class="topbar">
        <h1>Edit Project</h1>
        <a href="dashboard.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">← Back to Dashboard</a>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h2 class="section-title">✏️ Edit Project Entry</h2>

        <?php if(isset($error)){ ?>
            <div class="toast error"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <form method="POST">
            <!-- Output CSRF token input field -->
            <?php echo csrf_field(); ?>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">Project Title *</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required class="form-input" style="margin-bottom: 0;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">Project Description</label>
                <textarea name="description" class="form-input" style="margin-bottom: 0;"><?php echo htmlspecialchars($row['description']); ?></textarea>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">GitHub Link (URL)</label>
                <input type="url" name="github_link" value="<?php echo htmlspecialchars($row['github_link']); ?>" class="form-input" style="margin-bottom: 0;">
            </div>

            <button name="update" class="btn">Update Project</button>
        </form>
    </div>
</div>

</body>
</html>