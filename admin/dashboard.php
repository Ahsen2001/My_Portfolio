<?php
session_start();
include '../config.php';
include 'csrf.php';

// Authentication Guard
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Handle POST actions with CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_post_or_die();
}

// Project count
$countResult = $conn->query("SELECT COUNT(*) as total FROM projects");
$countRow = $countResult ? $countResult->fetch_assoc() : ['total' => 0];
$totalProjects = $countRow['total'];

// Get projects
$result = $conn->query("SELECT * FROM projects ORDER BY id DESC");

// CREATE ADMIN
if(isset($_POST['create_admin'])){
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if(empty($username) || empty($password)){
        $admin_error = "All fields are required!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM admins WHERE username=?");
        if ($check) {
            $check->bind_param("s", $username);
            $check->execute();
            $check->store_result();

            if($check->num_rows > 0){
                $admin_error = "Username already exists!";
            } else {
                $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ss", $username, $hashedPassword);
                    if($stmt->execute()){
                        $admin_success = "Admin created successfully!";
                    } else {
                        $admin_error = "Failed to create admin!";
                    }
                    $stmt->close();
                } else {
                    $admin_error = "Database error. Please try again.";
                }
            }
            $check->close();
        } else {
            $admin_error = "Database query failed.";
        }
    }
}

// Fetch Messages (with fallback support)
$messages = [];
$messages_db_active = false;
$msgResult = @$conn->query("SELECT * FROM messages ORDER BY id DESC LIMIT 50");
if ($msgResult) {
    $messages_db_active = true;
    while ($mRow = $msgResult->fetch_assoc()) {
        $messages[] = $mRow;
    }
} else {
    // Fallback: Read from contact_messages.txt if database table isn't created yet
    if (file_exists('../contact_messages.txt')) {
        $file_lines = array_reverse(file('../contact_messages.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        // Take top 20
        $file_lines = array_slice($file_lines, 0, 20);
        foreach ($file_lines as $line) {
            // Format: Y-m-d H:i:s - Name: X, Email: Y, Message: Z
            if (preg_match('/^([\d\-:\s]+) - Name: (.*?), Email: (.*?), Message: (.*)$/', $line, $matches)) {
                $messages[] = [
                    'created_at' => $matches[1],
                    'name' => $matches[2],
                    'email' => $matches[3],
                    'message' => $matches[4]
                ];
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
    <title>Admin Dashboard | Portfolio Manager</title>
    <!-- Google Font & Admin Stylesheet -->
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
    <h2>AHSEN <span>PORTFOLIO</span></h2>
    <a href="dashboard.php" class="active">🏠 Dashboard</a>
    <a href="add_project.php">➕ Add Project</a>
    <a href="../index.php" target="_blank">🌐 View Site</a>
    <a href="logout.php" style="margin-top: auto; color: var(--danger);">🚪 Logout</a>
</div>

<!-- Main Area -->
<div class="main fade">
    <div class="topbar">
        <h1>Dashboard Overview</h1>
        <input type="text" class="search" placeholder="Search projects..." onkeyup="searchProject(this.value)">
    </div>

    <!-- Stats summary -->
    <div class="stats">
        <div class="stat-card">
            <h3><?php echo $totalProjects; ?></h3>
            <p>Total Projects Listed</p>
        </div>
        <div class="stat-card">
            <h3><?php echo count($messages); ?></h3>
            <p>Recent Message Inquiries</p>
        </div>
    </div>

    <!-- Project List Management -->
    <div class="card">
        <h2 class="section-title">📂 Project Management</h2>
        
        <table id="projectTable">
            <thead>
                <tr>
                    <th>Project Title</th>
                    <th>GitHub Link</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()){ 
                ?>
                <tr>
                    <td style="font-weight: 600;"><?php echo htmlspecialchars($row['title']); ?></td>
                    <td style="color: var(--text-muted); font-size: 13px;">
                        <?php echo !empty($row['github_link']) ? htmlspecialchars($row['github_link']) : '<em>No link</em>'; ?>
                    </td>
                    <td style="text-align: right;">
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="edit">✏️ Edit</a>
                        <!-- Pass CSRF token safely in delete query parameters -->
                        <a href="delete.php?id=<?php echo $row['id']; ?>&csrf_token=<?php echo get_csrf_token(); ?>" 
                           class="delete"
                           onclick="return confirm('Are you sure you want to delete this project?')">🗑 Delete</a>
                    </td>
                </tr>
                <?php 
                    }
                } else { 
                ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 30px;">
                        No projects found. Click "Add Project" to create one.
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Client Message Inquiries -->
    <div class="card">
        <h2 class="section-title">✉️ Client Messages & Inquiries</h2>
        
        <?php if (!$messages_db_active) { ?>
            <div style="background: rgba(255, 193, 7, 0.08); border: 1px solid #ffc107; color: #ffc107; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">
                ⚠️ <strong>Note:</strong> Messages are currently falling back to log file storage because the database table <code>messages</code> does not exist yet. Please refer to database instructions.
            </div>
        <?php } ?>

        <table id="messageTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Sender Name</th>
                    <th>Email</th>
                    <th>Message Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($messages) > 0) {
                    foreach ($messages as $msg) {
                ?>
                <tr>
                    <td style="font-size: 13px; color: var(--text-muted); white-space: nowrap;">
                        <?php echo htmlspecialchars($msg['created_at'] ?? 'N/A'); ?>
                    </td>
                    <td style="font-weight: 600; white-space: nowrap;"><?php echo htmlspecialchars($msg['name']); ?></td>
                    <td>
                        <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" style="color: var(--primary); text-decoration: none;">
                            <?php echo htmlspecialchars($msg['email']); ?>
                        </a>
                    </td>
                    <td style="color: var(--text-muted); font-size: 14px; word-break: break-word;">
                        <?php echo htmlspecialchars($msg['message']); ?>
                    </td>
                </tr>
                <?php 
                    }
                } else { 
                ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">
                        No messages received yet.
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Admin User Management -->
    <div class="card">
        <h2 class="section-title">👤 Add Admin Account</h2>

        <?php if(isset($admin_error)){ ?>
            <div class="toast error"><?php echo htmlspecialchars($admin_error); ?></div>
        <?php } ?>

        <?php if(isset($admin_success)){ ?>
            <div class="toast success"><?php echo htmlspecialchars($admin_success); ?></div>
        <?php } ?>

        <form method="POST">
            <!-- Output CSRF helper field -->
            <?php echo csrf_field(); ?>
            
            <input type="text" name="username" placeholder="New Admin Username" required class="form-input">
            <input type="password" name="password" placeholder="Secure Password" required class="form-input">
            <button name="create_admin" class="btn">➕ Create Admin Account</button>
        </form>
    </div>
</div>

<script>
// Live table search filtering
function searchProject(input){
    let filter = input.toLowerCase();
    let rows = document.querySelectorAll("#projectTable tbody tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
}
</script>

</body>
</html>