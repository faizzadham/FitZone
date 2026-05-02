<?php
require_once 'includes/auth.php';
$pageTitle = 'Home';
require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content fade-in">
        <h1>Transform Your Body, Transform Your Life</h1>
        <p>Join FitZone Gym and start your fitness journey today. Professional equipment, expert trainers, and a supportive community await you.</p>
        <div class="hero-buttons">
            <?php if (!isLoggedIn()): ?>
                <a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Get Started</a>
                <a href="login.php" class="btn btn-secondary"><i class="fas fa-sign-in-alt"></i> Member Login</a>
            <?php else: ?>
                <?php if (getUserRole() === 'admin'): ?>
                    <a href="admin/dashboard.php" class="btn btn-primary"><i class="fas fa-tachometer-alt"></i> Go to Dashboard</a>
                <?php else: ?>
                    <a href="member/dashboard.php" class="btn btn-primary"><i class="fas fa-tachometer-alt"></i> My Dashboard</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="features">
    <div class="section-title">
        <h2>Why Choose FitZone?</h2>
        <p>Everything you need for your fitness goals</p>
    </div>
    <div class="features-grid">
        <div class="feature-card fade-in">
            <div class="icon"><i class="fas fa-dumbbell"></i></div>
            <h3>Modern Equipment</h3>
            <p>State-of-the-art fitness equipment for all your workout needs.</p>
        </div>
        <div class="feature-card fade-in">
            <div class="icon"><i class="fas fa-users"></i></div>
            <h3>Expert Trainers</h3>
            <p>Certified personal trainers to guide your fitness journey.</p>
        </div>
        <div class="feature-card fade-in">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <h3>Flexible Hours</h3>
            <p>Open early to late so you can work out on your schedule.</p>
        </div>
        <div class="feature-card fade-in">
            <div class="icon"><i class="fas fa-tag"></i></div>
            <h3>Affordable Plans</h3>
            <p>Multiple membership packages to fit every budget.</p>
        </div>
        <div class="feature-card fade-in">
            <div class="icon"><i class="fas fa-heart"></i></div>
            <h3>Community</h3>
            <p>Join a supportive community of fitness enthusiasts.</p>
        </div>
        <div class="feature-card fade-in">
            <div class="icon"><i class="fas fa-shield-alt"></i></div>
            <h3>Safe & Clean</h3>
            <p>Sanitized facilities and safety protocols for your peace of mind.</p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
