/* ==========================================================================
   Portfolio Dynamic Interactivity Script
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    
    // === 1. Navbar Sticky & Scroll Effects ===
    const nav = document.querySelector('nav');
    const sections = document.querySelectorAll('.section, .hero-wrapper');
    const navLinks = document.querySelectorAll('nav ul li a');

    window.addEventListener('scroll', () => {
        // Sticky transition
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }

        // Active state navigation highlighting
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (window.scrollY >= (sectionTop - 150)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });

    // === 2. Mobile Responsive Menu ===
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('nav ul');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            navMenu.classList.toggle('active');
        });

        // Close menu when a link is clicked
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                menuToggle.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }

    // === 3. Scroll Reveal (Fade-in animations) ===
    const revealOnScroll = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                // Once visible, no need to track it further
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15
    });

    sections.forEach(section => {
        // Exclude visible states by default if they are inside hero wrapper initially
        if (!section.classList.contains('hero-wrapper')) {
            revealOnScroll.observe(section);
        } else {
            section.classList.add('visible');
        }
    });

    // Make sections visible initially if they don't have JavaScript running
    document.querySelectorAll('.section').forEach(section => {
        section.classList.add('reveal-ready'); // ready for observer
    });

    // === 4. AJAX Contact Form Submission ===
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerText;
            
            // Loading state
            submitBtn.disabled = true;
            submitBtn.innerText = 'Sending...';

            const formData = new FormData(this);
            formData.append('action', 'contact');

            fetch('index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    contactForm.reset();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Something went wrong. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            });
        });
    }

    // === 5. Toast Notifications ===
    function showToast(message, type) {
        // Remove existing toast if visible
        const existingToast = document.querySelector('.toast-msg');
        if (existingToast) {
            existingToast.remove();
        }

        const toast = document.createElement('div');
        toast.className = `toast-msg toast-${type}`;
        toast.innerText = message;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        // Animate out and remove
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 400);
        }, 4000);
    }
});
