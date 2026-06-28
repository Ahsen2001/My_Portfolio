<?php
session_start();
include '../config.php';
include 'csrf.php';

// Authentication Guard
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Fetch Profile
$profile_query = $conn->query("SELECT * FROM profile LIMIT 1");
$profile = $profile_query ? $profile_query->fetch_assoc() : null;

// Handle Profile Update
if (isset($_POST['update_profile'])) {
    validate_csrf_post_or_die();
    
    $name = trim($_POST['name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if (empty($name) || empty($title) || empty($bio)) {
        $error = "All fields are required!";
    } else {
        if ($profile) {
            $stmt = $conn->prepare("UPDATE profile SET name=?, title=?, bio=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $title, $bio, $profile['id']);
        } else {
            $stmt = $conn->prepare("INSERT INTO profile (name, title, bio) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $title, $bio);
        }
        
        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            // Reload profile data
            $profile_query = $conn->query("SELECT * FROM profile LIMIT 1");
            $profile = $profile_query->fetch_assoc();
        } else {
            $error = "Failed to update profile details.";
        }
        $stmt->close();
    }
}

// Handle Add Skill
if (isset($_POST['add_skill'])) {
    validate_csrf_post_or_die();
    
    $new_skill = trim($_POST['skill_name'] ?? '');
    if (empty($new_skill)) {
        $skill_error = "Skill tag cannot be empty.";
    } else {
        $stmt = $conn->prepare("INSERT IGNORE INTO skills (name) VALUES (?)");
        $stmt->bind_param("s", $new_skill);
        if ($stmt->execute()) {
            $skill_success = "Skill added successfully!";
        } else {
            $skill_error = "Failed to save skill tag.";
        }
        $stmt->close();
    }
}

// Handle Delete Skill (GET request with CSRF)
if (isset($_GET['delete_skill']) && is_numeric($_GET['delete_skill'])) {
    if (isset($_GET['csrf_token']) && verify_csrf_token($_GET['csrf_token'])) {
        $skill_id = intval($_GET['delete_skill']);
        $stmt = $conn->prepare("DELETE FROM skills WHERE id = ?");
        $stmt->bind_param("i", $skill_id);
        $stmt->execute();
        $stmt->close();
        header("Location: edit_profile.php?skill_deleted=1");
        exit();
    } else {
        die("CSRF verification failed.");
    }
}

// Fetch all skills
$skills_result = $conn->query("SELECT * FROM skills ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile & Skills | Portfolio CMS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
    <h2>AHSEN <span>CMS</span></h2>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="edit_profile.php" class="active">👤 Profile & Skills</a>
    <a href="manage_timeline.php">⏳ Timeline Info</a>
    <a href="manage_certs.php">🏆 Certifications</a>
    <a href="add_project.php">➕ Add Project</a>
    <a href="../index.php" target="_blank">🌐 View Site</a>
    <a href="logout.php" style="margin-top: auto; color: var(--danger);">🚪 Logout</a>
</div>

<!-- Main Area -->
<div class="main fade">
    <div class="topbar">
        <h1>Profile & Skills Manager</h1>
    </div>

    <!-- Edit Profile Card -->
    <div class="card">
        <h2 class="section-title">👤 Edit Profile Information</h2>
        
        <?php if(isset($error)){ ?>
            <div class="toast error"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <?php if(isset($success)){ ?>
            <div class="toast success"><?php echo htmlspecialchars($success); ?></div>
        <?php } ?>

        <form method="POST">
            <?php echo csrf_field(); ?>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">Full Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($profile['name'] ?? ''); ?>" required class="form-input" style="margin-bottom: 0;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">Professional Title *</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($profile['title'] ?? ''); ?>" required class="form-input" style="margin-bottom: 0;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">Bio Summary *</label>
                <textarea name="bio" required class="form-input" style="margin-bottom: 0; min-height: 150px;"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
            </div>

            <button name="update_profile" class="btn">Update Profile Details</button>
        </form>
    </div>

    <!-- Skills Tag Manager -->
    <div class="card">
        <h2 class="section-title">🛠️ Manage Skill Tags</h2>

        <?php if(isset($skill_error)){ ?>
            <div class="toast error"><?php echo htmlspecialchars($skill_error); ?></div>
        <?php } ?>

        <?php if(isset($skill_success) || isset($_GET['skill_deleted'])){ ?>
            <div class="toast success">Skills list updated successfully!</div>
        <?php } ?>

        <!-- List current skills with quick delete buttons -->
        <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 30px;">
            <?php 
            if ($skills_result && $skills_result->num_rows > 0) {
                while ($skill = $skills_result->fetch_assoc()) {
            ?>
                <div style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-glass); padding: 8px 16px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px;">
                    <span style="font-size: 14px; font-weight: 500;"><?php echo htmlspecialchars($skill['name']); ?></span>
                    <a href="edit_profile.php?delete_skill=<?php echo $skill['id']; ?>&csrf_token=<?php echo get_csrf_token(); ?>" 
                       style="color: var(--danger); text-decoration: none; font-size: 13px; font-weight: bold;" 
                       onclick="return confirm('Remove skill tag: <?php echo htmlspecialchars($skill['name']); ?>?')"
                       title="Delete skill">✕</a>
                </div>
            <?php 
                }
            } else {
            ?>
                <p style="color: var(--text-muted);">No skills tags created yet.</p>
            <?php } ?>
        </div>

        <!-- Add Skill Form -->
        <form method="POST" style="display: flex; gap: 15px; align-items: flex-end;">
            <?php echo csrf_field(); ?>
            <div style="flex: 1;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">Add New Skill Tag</label>
                <input type="text" name="skill_name" placeholder="e.g. TailwindCSS, Python, Docker" required class="form-input" style="margin: 5px 0 0 0;">
            </div>
            <button name="add_skill" class="btn" style="width: auto; padding: 14px 30px;">Add Tag</button>
        </form>
    </div>
</div>

</body>
</html>
