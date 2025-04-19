<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>Our Lost & Found system helps connect people who have lost items with those who have found them. We aim to make the process of recovering lost items as simple as possible.</p>
                <div class="footer-social">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="/Lost_Found_System/index.php">Home</a></li>
                    <li><a href="/Lost_Found_System/pages/dashboard.php">Dashboard</a></li>
                    <li><a href="/Lost_Found_System/pages/report_lost.php">Report Lost</a></li>
                    <li><a href="/Lost_Found_System/pages/report_found.php">Report Found</a></li>
                    <li><a href="/Lost_Found_System/pages/contact_us.php">Contact Us</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Contact Us</h3>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Main Street, City</li>
                    <li><i class="fas fa-phone"></i> (123) 456-7890</li>
                    <li><i class="fas fa-envelope"></i> info@lostandfound.com</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Lost & Found System. All rights reserved.</p>
        </div>
    </div>
    
    <script>
        // Mobile Navigation Toggle
        const mobileToggle = document.getElementById('mobile-toggle');
        const navLinks = document.getElementById('nav-links');
        
        mobileToggle?.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    </script>
</footer>