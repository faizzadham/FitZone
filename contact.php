<?php
require_once 'includes/auth.php';
$pageTitle = 'Contact Us';
require_once 'includes/header.php';

$msgSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msgSent = true;
}
?>

<div class="section-title" style="padding-top: 3rem;">
    <h2>Contact Us</h2>
    <p>Have questions? We'd love to hear from you.</p>
</div>

<div class="contact-grid fade-in">
    <div class="contact-info-card">
        <h3>Get In Touch</h3>
        <div class="contact-item">
            <div class="ci-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div>
                <strong>Address</strong>
                <p>123 Fitness Street, Kuala Lumpur, Malaysia</p>
            </div>
        </div>
        <div class="contact-item">
            <div class="ci-icon"><i class="fas fa-phone"></i></div>
            <div>
                <strong>Phone</strong>
                <p>+60 12-345 6789</p>
            </div>
        </div>
        <div class="contact-item">
            <div class="ci-icon"><i class="fas fa-envelope"></i></div>
            <div>
                <strong>Email</strong>
                <p>info@fitzone.com</p>
            </div>
        </div>
        <div class="contact-item">
            <div class="ci-icon"><i class="fas fa-clock"></i></div>
            <div>
                <strong>Operating Hours</strong>
                <p>Mon - Sat: 6:00 AM - 10:00 PM<br>Sun: 8:00 AM - 6:00 PM</p>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 1rem;">Send a Message</h3>
        <?php if ($msgSent): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> Thank you! Your message has been received.</div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter your name">
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required placeholder="Enter subject">
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" required placeholder="Write your message..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"><i class="fas fa-paper-plane"></i> Send Message</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
