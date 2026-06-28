<?php
session_start();
include '../config.php';
include 'csrf.php';

// Authentication Guard
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Handle Form Submissions
$msg_success = '';
$msg_error = '';

// Add / Edit Timeline Item
if (isset($_POST['save_timeline'])) {
    validate_csrf_post_or_die();

    $type = $_POST['type'] ?? 'experience';
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $duration = trim($_POST['duration_dates'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $edit_id = $_POST['edit_id'] ?? '';

    if (empty($title) || empty($subtitle) || empty($duration)) {
        $msg_error = "Title, Subtitle/Institution, and Duration dates are required!";
    } else {
        if (!empty($edit_id) && is_numeric($edit_id)) {
            // Update
            $stmt = $conn->prepare("UPDATE timeline SET type=?, title=?, subtitle=?, duration_dates=?, description=? WHERE id=?");
            $stmt->bind_param("sssssi", $type, $title, $subtitle, $duration, $description, $edit_id);
            if ($stmt->execute()) {
                $msg_success = "Timeline entry updated successfully!";
            } else {
                $msg_error = "Failed to update timeline entry.";
            }
            $stmt->close();
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO timeline (type, title, subtitle, duration_dates, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $type, $title, $subtitle, $duration, $description);
            if ($stmt->execute()) {
                $msg_success = "Timeline entry added successfully!";
            } else {
                $msg_error = "Failed to save timeline entry.";
            }
            $stmt->close();
        }
    }
}

// Delete Timeline Item
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (isset($_GET['csrf_token']) && verify_csrf_token($_GET['csrf_token'])) {
        $delete_id = intval($_GET['delete']);
        $stmt = $conn->prepare("DELETE FROM timeline WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_timeline.php?deleted=1");
        exit();
    } else {
        die("CSRF verification failed.");
    }
}

// Fetch single item if editing
$edit_item = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM timeline WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_item = $edit_result->fetch_assoc();
    $stmt->close();
}

// Fetch all timeline entries
$entries_result = $conn->query("SELECT * FROM timeline ORDER BY type ASC, id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Timeline | Portfolio CMS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
    <h2>AHSEN <span>CMS</span></h2>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="edit_profile.php">👤 Profile & Skills</a>
    <a href="manage_timeline.php" class="active">⏳ Timeline Info</a>
    <a href="manage_certs.php">🏆 Certifications</a>
    <a href="add_project.php">➕ Add Project</a>
    <a href="../index.php" target="_blank">🌐 View Site</a>
    <a href="logout.php" style="margin-top: auto; color: var(--danger);">🚪 Logout</a>
</div>

<!-- Main Area -->
<div class="main fade">
    <div class="topbar">
        <h1>Timeline Manager</h1>
    </div>

    <!-- Add/Edit Entry Form Card -->
    <div class="card" style="max-width: 700px; margin: 0 auto 30px auto;">
        <h2 class="section-title"><?php echo $edit_item ? '✏️ Edit Timeline Entry' : '✨ Add Timeline Entry'; ?></h2>
        
        <?php if(!empty($msg_error)){ ?>
            <div class="toast error"><?php echo htmlspecialchars($msg_error); ?></div>
        <?php } ?>

        <?php if(!empty($msg_success) || isset($_GET['deleted'])){ ?>
            <div class="toast success">Timeline entries database updated!</div>
        <?php } ?>

        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($edit_item['id'] ?? ''); ?>">

            <div style="margin-bottom: 15px;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">Entry Type *</label>
                <select name="type" class="form-input" style="margin-bottom: 0;">
                    <option value="experience" <?php echo (isset($edit_item['type']) && $edit_item['type'] === 'experience') ? 'selected' : ''; ?>>Work Experience</option>
                    <option value="education" <?php echo (isset($edit_item['type']) && $edit_item['type'] === 'education') ? 'selected' : ''; ?>>Education History</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">Title / Role / Degree *</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($edit_item['title'] ?? ''); ?>" placeholder="e.g. Senior Tutor, BA (Hons) ICT" required class="form-input" style="margin-bottom: 0;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">Institution / Organization *</label>
                <input type="text" name="subtitle" value="<?php echo htmlspecialchars($edit_item['subtitle'] ?? ''); ?>" placeholder="e.g. BRIGHT MINDS COLLEGE, ATI Batticaloa" required class="form-input" style="margin-bottom: 0;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">Duration dates *</label>
                <input type="text" name="duration_dates" value="<?php echo htmlspecialchars($edit_item['duration_dates'] ?? ''); ?>" placeholder="e.g. Nov 2024 - 2025, Jul 2022 - Present" required class="form-input" style="margin-bottom: 0;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="font-weight: 500; color: var(--text-muted); font-size: 14px;">Description / Duties</label>
                <textarea name="description" class="form-input" placeholder="Summarize achievements, courses, or core duties..." style="margin-bottom: 0; min-height: 100px;"><?php echo htmlspecialchars($edit_item['description'] ?? ''); ?></textarea>
            </div>

            <div style="display: flex; gap: 15px;">
                <button name="save_timeline" class="btn" style="flex: 2;"><?php echo $edit_item ? 'Update Entry' : 'Add Entry'; ?></button>
                <?php if ($edit_item) { ?>
                    <a href="manage_timeline.php" class="btn" style="flex: 1; text-align: center; text-decoration: none; background: rgba(255,255,255,0.05); border: 1px solid var(--border-glass); color: white; display: flex; align-items: center; justify-content: center; box-shadow: none;">Cancel</a>
                <?php } ?>
            </div>
        </form>
    </div>

    <!-- Timeline Entries List -->
    <div class="card">
        <h2 class="section-title">📂 Timeline Milestones</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Title & Institution</th>
                    <th>Duration</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($entries_result && $entries_result->num_rows > 0) {
                    while ($row = $entries_result->fetch_assoc()) {
                ?>
                <tr>
                    <td>
                        <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; padding: 4px 8px; border-radius: 4px; background: <?php echo $row['type'] === 'experience' ? 'rgba(0,198,255,0.1)' : 'rgba(255,0,127,0.1)'; ?>; color: <?php echo $row['type'] === 'experience' ? 'var(--primary)' : '#ff007f'; ?>;">
                            <?php echo htmlspecialchars($row['type']); ?>
                        </span>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                        <div style="font-size: 13px; color: var(--text-muted);"><?php echo htmlspecialchars($row['subtitle']); ?></div>
                    </td>
                    <td style="font-size: 14px; white-space: nowrap;"><?php echo htmlspecialchars($row['duration_dates']); ?></td>
                    <td style="text-align: right; white-space: nowrap;">
                        <a href="manage_timeline.php?edit=<?php echo $row['id']; ?>" class="edit">✏️ Edit</a>
                        <a href="manage_timeline.php?delete=<?php echo $row['id']; ?>&csrf_token=<?php echo get_csrf_token(); ?>" 
                           class="delete"
                           onclick="return confirm('Delete timeline entry: <?php echo htmlspecialchars($row['title']); ?>?')">🗑 Delete</a>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">
                        No timeline records found.
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
