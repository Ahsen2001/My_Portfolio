<?php
include 'config.php';

// Handle contact AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'contact') {
    header('Content-Type: application/json');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
        exit;
    }

    // Attempt message storage in database
    $stmt = @$conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $name, $email, $message);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Your message has been sent successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save message to the database.']);
        }
        $stmt->close();
    } else {
        error_log("Messages table missing or insert preparation failed. Message: From $name ($email).");
        $log_entry = date('Y-m-d H:i:s') . " - Name: $name, Email: $email, Message: $message\n";
        @file_put_contents('contact_messages.txt', $log_entry, FILE_APPEND);
        echo json_encode(['status' => 'success', 'message' => 'Your message has been received successfully!']);
    }
    exit;
}

// Secure URL checker to prevent script/XSS injection in links
function safe_url($url) {
    $url = trim($url);
    if (empty($url)) return '#';
    $parsed = parse_url($url);
    if (isset($parsed['scheme']) && in_array(strtolower($parsed['scheme']), ['http', 'https'])) {
        return $url;
    }
    if (!isset($parsed['scheme']) && !empty($url)) {
        return 'https://' . ltrim($url, '/');
    }
    return '#';
}

// 1. Fetch Profile Information
$profile_res = $conn->query("SELECT * FROM profile LIMIT 1");
$profile = ($profile_res && $profile_res->num_rows > 0) ? $profile_res->fetch_assoc() : [
    'name' => 'Umer Ahsen',
    'title' => 'Full Stack Web Developer',
    'bio' => 'A motivated and detail-oriented web development graduate with strong hands-on experience in HTML, CSS, JavaScript, PHP, and MySQL. Passionate about building interactive, scalable, and secure applications. I am currently pursuing my BA (Hons) in ICT at the South Eastern University of Sri Lanka.',
    'profile_image' => 'image/Profile.jpg'
];

// 2. Fetch Skill Tags
$skills_list = [];
$skills_res = $conn->query("SELECT * FROM skills ORDER BY id ASC");
if ($skills_res && $skills_res->num_rows > 0) {
    while ($sRow = $skills_res->fetch_assoc()) {
        $skills_list[] = $sRow['name'];
    }
} else {
    // Fallback static seeds
    $skills_list = ["HTML / CSS / Javascript", "PHP & MySQL", "ReactJS", "Bootstrap & Responsive Design", "Git & GitHub Workflow"];
}

// 3. Fetch Certifications
$certs_list = [];
$certs_res = $conn->query("SELECT * FROM certifications ORDER BY id ASC");
if ($certs_res && $certs_res->num_rows > 0) {
    while ($cRow = $certs_res->fetch_assoc()) {
        $certs_list[] = $cRow['name'];
    }
} else {
    // Fallback static seeds
    $certs_list = ["SLASSCOM Fundamentals", "Mobile Phone Repair Technician", "Security & Surveillance Technician", "IT Fundamentals"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($profile['name']); ?>'s professional web development portfolio displaying projects, skills, education, and contact form.">
    <title><?php echo htmlspecialchars($profile['name']); ?> | <?php echo htmlspecialchars($profile['title']); ?></title>
    
    <!-- Link External Stylesheet -->
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="image/Profile.jpg">
</head>
<body>

    <!-- NAVBAR -->
    <nav id="navbar">
        <a href="#home" class="logo"><?php 
            $name_parts = explode(' ', $profile['name'], 2);
            echo htmlspecialchars(strtoupper($name_parts[0])); 
            if (isset($name_parts[1])) {
                echo ' <span>' . htmlspecialchars(strtoupper($name_parts[1])) . '</span>';
            }
        ?></a>
        
        <!-- Hamburger Menu Toggle -->
        <div class="menu-toggle" aria-label="Toggle Navigation menu" role="button">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <ul>
            <li><a href="#home" class="active">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#projects">Projects</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>

    <!-- HERO SECTION -->
    <header class="hero-wrapper" id="home">
        <div class="hero-content">
            <h1>Hi, I'm <span class="gradient-text-alt"><?php echo htmlspecialchars($profile['name']); ?></span></h1>
            <p><?php echo htmlspecialchars($profile['title']); ?> crafting secure, high-performance, and responsive web applications.</p>
            <div class="hero-buttons">
                <a href="#projects" class="btn-primary">View Projects</a>
                <a href="Umer_Ahsen_CV.pdf" download class="btn-secondary">Download CV</a>
            </div>
        </div>
    </header>

    <main>
        <!-- ABOUT & PROFILE SECTION -->
        <section class="section" id="about">
            <h2 class="section-header">About Me</h2>
            <div class="profile-grid">
                <div class="profile-img-container">
                    <img src="<?php echo htmlspecialchars($profile['profile_image']); ?>" alt="<?php echo htmlspecialchars($profile['name']); ?> Profile Photo">
                </div>
                <div class="profile-details">
                    <h3>Who I Am</h3>
                    <p><?php echo htmlspecialchars($profile['bio']); ?></p>
                    
                    <h3 style="margin-top: 20px; margin-bottom: 15px;">Technical Stack</h3>
                    <div class="skills-tags">
                        <?php foreach ($skills_list as $skill) { ?>
                            <span><?php echo htmlspecialchars($skill); ?></span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- PROJECTS SECTION -->
        <section class="section" id="projects">
            <h2 class="section-header">My Projects</h2>
            <div class="projects-grid">
                <?php
                $result = $conn->query("SELECT * FROM projects ORDER BY id DESC");
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()){
                ?>
                    <div class="glass-card project-card">
                        <div>
                            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                        <a href="<?php echo htmlspecialchars(safe_url($row['github_link'])); ?>" target="_blank" rel="noopener" class="project-link">View Project</a>
                    </div>
                <?php 
                    }
                } else {
                ?>
                    <div class="glass-card" style="grid-column: 1/-1; text-align: center; padding: 40px;">
                        <p style="color: var(--text-muted);">No projects displayed yet. Check back soon!</p>
                    </div>
                <?php } ?>
            </div>
        </section>

        <!-- EXPERIENCE & EDUCATION SECTION -->
        <section class="section" id="timeline">
            <h2 class="section-header">Experience & Education</h2>
            <div class="timeline">
                <?php
                $timeline_res = $conn->query("SELECT * FROM timeline ORDER BY type ASC, id DESC");
                if ($timeline_res && $timeline_res->num_rows > 0) {
                    while ($t = $timeline_res->fetch_assoc()) {
                ?>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <span class="timeline-date"><?php echo htmlspecialchars($t['duration_dates']); ?></span>
                            <h3>
                                <?php echo htmlspecialchars($t['title']); ?> | <?php echo htmlspecialchars($t['subtitle']); ?>
                                <span style="font-size: 10px; text-transform: uppercase; font-weight: 700; padding: 2px 6px; border-radius: 4px; background: <?php echo $t['type'] === 'experience' ? 'rgba(0,198,255,0.1)' : 'rgba(255,0,127,0.1)'; ?>; color: <?php echo $t['type'] === 'experience' ? 'var(--primary)' : '#ff007f'; ?>; margin-left: 8px; vertical-align: middle;">
                                    <?php echo htmlspecialchars($t['type']); ?>
                                </span>
                            </h3>
                            <?php if (!empty($t['description'])) { ?>
                                <p><?php echo htmlspecialchars($t['description']); ?></p>
                            <?php } ?>
                        </div>
                    </div>
                <?php 
                    }
                } else {
                ?>
                    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                        No timeline records listed yet.
                    </div>
                <?php } ?>
            </div>
        </section>

        <!-- CERTIFICATIONS SECTION -->
        <section class="section" id="certifications">
            <h2 class="section-header">Certifications</h2>
            <div class="certifications-container">
                <?php foreach ($certs_list as $cert) { ?>
                    <div class="cert-badge">
                        <span><?php echo htmlspecialchars($cert); ?></span>
                    </div>
                <?php } ?>
            </div>
        </section>

        <!-- CONTACT SECTION -->
        <section class="section" id="contact">
            <h2 class="section-header">Get In Touch</h2>
            <div class="glass-card contact-container">
                <form class="contact-form" id="contactForm">
                    <div class="form-group">
                        <label for="formName">Your Name</label>
                        <input type="text" id="formName" name="name" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label for="formEmail">Your Email</label>
                        <input type="email" id="formEmail" name="email" placeholder="john@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="formMessage">Your Message</label>
                        <textarea id="formMessage" name="message" rows="5" placeholder="I'd love to discuss a project..." required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2026 <?php echo htmlspecialchars($profile['name']); ?>. All rights reserved.</p>
        <p style="margin-top: 10px;">Connect on <a href="https://www.linkedin.com/in/umerahsen" target="_blank" rel="noopener">LinkedIn</a> | Admin <a href="admin/login.php">Dashboard</a></p>
    </footer>

    <!-- Link External Interactivity Script -->
    <script src="script.js"></script>
</body>
</html>