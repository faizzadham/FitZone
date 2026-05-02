    </main>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-dumbbell"></i>
                        <span>FitZone</span>
                    </div>
                    <p>Your premier fitness destination. Building stronger bodies and healthier lives since 2020.</p>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="/Gymmanagement/index.php">Home</a></li>
                        <li><a href="/Gymmanagement/about.php">About</a></li>
                        <li><a href="/Gymmanagement/contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contact Info</h4>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Fitness Street, KL</li>
                        <li><i class="fas fa-phone"></i> +60 12-345 6789</li>
                        <li><i class="fas fa-envelope"></i> info@fitzone.com</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Follow Us</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> FitZone Gym. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script>
        // Mobile nav toggle
        const navToggle = document.getElementById('navToggle');
        const navMenu = document.getElementById('navMenu');
        if (navToggle) {
            navToggle.addEventListener('click', () => {
                navMenu.classList.toggle('active');
                navToggle.classList.toggle('active');
            });
        }
    </script>
</body>
</html>
