<?php
session_start();
include '../config.php';
include 'csrf.php';

// Authentication Guard
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$msg_success = '';
$msg_error = '';

// Add Certification
if (isset($_POST['add_cert'])) {
    validate_csrf_post_or_die();
    
    $name = trim($_POST['name'] ?? '');
    
    if (empty($name)) {
        $msg_error = "Certification name cannot be empty!";
    } else {
        $stmt = $conn->prepare("INSERT IGNORE INTO certifications (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $msg_success = "Certification tag added successfully!";
        } else {
            $msg_error = "Failed to add certification.";
        }
        $stmt->close();
    }
}

// Delete Certification
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (isset($_GET['csrf_token']) && verify_csrf_token($_GET['csrf_token'])) {
        $delete_id = intval($_GET['delete']);
        $stmt = $conn->prepare("DELETE FROM certifications WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_certs.php?deleted=1");
        exit();
    } else {
        die("CSRF verification failed.");
    }
}

// Fetch all certifications
$certs_result = $conn->query("SELECT * FROM certifications ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Certifications | Portfolio CMS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
    <h2>AHSEN <span>CMS</span></h2>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="edit_profile.php">👤 Profile & Skills</a>
    <a href="manage_timeline.php">⏳ Timeline Info</a>
    <a href="manage_certs.php" class="active">🏆 Certifications</a>
    <a href="add_project.php">➕ Add Project</a>
    <a href="../index.php" target="_blank">🌐 View Site</a>
    <a href="logout.php" style="margin-top: auto; color: var(--danger);">🚪 Logout</a>
</div>

<!-- Main Area -->
<div class="main fade">
    <div class="topbar">
        <h1>Certifications Manager</h1>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto 30px auto;">
        <h2 class="section-title">🏆 Add Certification Badge</h2>
        
        <?php if(!empty($msg_error)){ ?>
            <div class="toast error"><?php echo htmlspecialchars($msg_error); ?></div>
        <?php } ?>

        <?php if(!empty($msg_success) || isset($_GET['deleted'])){ ?>
            <div class="toast success">Certifications list updated successfully!</div>
        <?php } ?>

        <form method="POST" style="display: flex; gap: 15px; align-items: flex-end;">
            <?php echo csrf_field(); ?>
            <div style="flex: 1;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">Certification Name</label>
                <input type="text" name="name" placeholder="e.g. SLASSCOM Fundamentals, AWS Cloud Practitioner" required class="form-input" style="margin: 5px 0 0 0;">
            </div>
            <button name="add_cert" class="btn" style="width: auto; padding: 14px 30px;">Add Badge</button>
        </form>
    </div>

    <!-- Certifications List Card -->
    <div class="card">
        <h2 class="section-title">📂 Active Badges</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Certification Title</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($certs_result && $certs_result->num_rows > 0) {
                    while ($row = $certs_result->fetch_assoc()) {
                ?>
                <tr>
                    <td style="font-weight: 600;"><?php echo htmlspecialchars($row['name']); ?></td>
                    <td style="text-align: right;">
                        <a href="manage_certs.php?delete=<?php echo $row['id']; ?>&csrf_token=<?php echo get_csrf_token(); ?>" 
                           class="delete"
                           onclick="return confirm('Remove certification badge: <?php echo htmlspecialchars($row['name']); ?>?')">🗑 Delete</a>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                ?>
                <tr>
                    <td colspan="2" style="text-align: center; color: var(--text-muted); padding: 30px;">
                        No certification badges found.
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
