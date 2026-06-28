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
    // We try/catch or suppress errors in case the database table doesn't exist yet
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
        // Log locally if db failed, but fallback to file or generic success to keep user UX clean
        error_log("Messages table missing or insert preparation failed. Message: From $name ($email).");
        // Save to fallback file to prevent loss of message
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
        // If it starts with github.com or similar
        return 'https://' . ltrim($url, '/');
    }
    return '#';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Umer Ahsen's professional web development portfolio displaying projects, skills, education, and contact form.">
    <title>Umer Ahsen | Full Stack Web Developer</title>
    
    <!-- Link External Stylesheet -->
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="image/Profile.jpg">
</head>
<body>

    <!-- NAVBAR -->
    <nav id="navbar">
        <a href="#home" class="logo">UMER <span>AHSEN</span></a>
        
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
            <h1>Hi, I'm <span class="gradient-text-alt">Umer Ahsen</span></h1>
            <p>Full Stack Web Developer crafting secure, high-performance, and responsive web applications.</p>
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
                    <img src="image/Profile.jpg" alt="Umer Ahsen Profile Photo">
                </div>
                <div class="profile-details">
                    <h3>Who I Am</h3>
                    <p>A motivated and detail-oriented web development graduate with strong hands-on experience in HTML, CSS, JavaScript, PHP, and MySQL. Passionate about building interactive, scalable, and secure applications. I am currently pursuing my BA (Hons) in ICT at the South Eastern University of Sri Lanka.</p>
                    
                    <h3 style="margin-top: 20px; margin-bottom: 15px;">Technical Stack</h3>
                    <div class="skills-tags">
                        <span>HTML / CSS / Javascript</span>
                        <span>PHP & MySQL</span>
                        <span>ReactJS</span>
                        <span>Bootstrap & Responsive Design</span>
                        <span>Git & GitHub Workflow</span>
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
                <div class="timeline-item">
                    <div class="timeline-content">
                        <span class="timeline-date">Nov 2024 - 2025</span>
                        <h3>Tutor | BRIGHT MINDS COLLEGE</h3>
                        <p>Instructor leading the Diploma in Computer Basics program, preparing students with foundational computer literacy and systems training.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <span class="timeline-date">Feb 2022 - 2024</span>
                        <h3>Teacher | AN NOOR ACADEMY</h3>
                        <p>Delivered student-centered lessons and curriculum guidance over two academic years of school instruction.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <span class="timeline-date">Jul 2022 - Present</span>
                        <h3>BA (Hons) ICT | South Eastern University of Sri Lanka</h3>
                        <p>Currently pursuing professional qualifications, with specialization in modern computing methods and information technology systems.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <span class="timeline-date">Apr 2022 - 2025</span>
                        <h3>HNDIT | ATI Batticaloa</h3>
                        <p>Completed Advanced National Diploma in Information Technology, focusing on software engineering, database design, and systems architecture.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CERTIFICATIONS SECTION -->
        <section class="section" id="certifications">
            <h2 class="section-header">Certifications</h2>
            <div class="certifications-container">
                <div class="cert-badge">
                    <span>SLASSCOM Fundamentals</span>
                </div>
                <div class="cert-badge">
                    <span>Mobile Phone Repair Technician</span>
                </div>
                <div class="cert-badge">
                    <span>Security & Surveillance Technician</span>
                </div>
                <div class="cert-badge">
                    <span>IT Fundamentals</span>
                </div>
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
        <p>&copy; 2026 Umer Ahsen. All rights reserved.</p>
        <p style="margin-top: 10px;">Connect on <a href="https://www.linkedin.com/in/umerahsen" target="_blank" rel="noopener">LinkedIn</a> | Admin <a href="admin/login.php">Dashboard</a></p>
    </footer>

    <!-- Link External Interactivity Script -->
    <script src="script.js"></script>
</body>
</html>