<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FitZone Gym - Professional Gym Membership Management System">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | FitZone Gym' : 'FitZone Gym'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/Gymmanagement/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/Gymmanagement/index.php" class="nav-logo">
                <i class="fas fa-dumbbell"></i>
                <span>FitZone</span>
            </a>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span></span><span></span><span></span>
            </button>
            <ul class="nav-menu" id="navMenu">
                <li><a href="/Gymmanagement/index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="/Gymmanagement/about.php"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="/Gymmanagement/contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                <?php if (isLoggedIn()): ?>
                    <?php if (getUserRole() === 'admin'): ?>
                        <li><a href="/Gymmanagement/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="/Gymmanagement/member/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="/Gymmanagement/logout.php" class="btn-nav btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="/Gymmanagement/login.php" class="btn-nav"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="/Gymmanagement/register.php" class="btn-nav btn-register"><i class="fas fa-user-plus"></i> Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <main class="main-content">
