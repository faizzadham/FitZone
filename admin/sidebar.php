<div class="sidebar">
    <div class="sidebar-label">Main</div>
    <ul class="sidebar-menu">
        <li><a href="/FitZone/admin/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    </ul>
    <div class="sidebar-label">Management</div>
    <ul class="sidebar-menu">
        <li><a href="/FitZone/admin/members.php" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['members.php','member_add.php','member_edit.php']) ? 'active' : ''; ?>"><i class="fas fa-users"></i> Members</a></li>
        <li><a href="/FitZone/admin/packages.php" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['packages.php','package_add.php','package_edit.php']) ? 'active' : ''; ?>"><i class="fas fa-box"></i> Packages</a></li>
        <li><a href="/FitZone/admin/payments.php" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['payments.php','payment_add.php','payment_edit.php']) ? 'active' : ''; ?>"><i class="fas fa-credit-card"></i> Payments</a></li>
    </ul>
    <div class="sidebar-label">Personal Training</div>
    <ul class="sidebar-menu">
        <li><a href="/FitZone/admin/trainers.php" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['trainers.php','trainer_add.php','trainer_edit.php']) ? 'active' : ''; ?>"><i class="fas fa-user-tie"></i> Trainers</a></li>
        <li><a href="/FitZone/admin/bookings.php" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['bookings.php','booking_edit.php']) ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i> Bookings</a></li>
    </ul>
    <div class="sidebar-label">Analytics</div>
    <ul class="sidebar-menu">
        <li><a href="/FitZone/admin/reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Reports</a></li>
    </ul>
    <div class="sidebar-label">Account</div>
    <ul class="sidebar-menu">
        <li><a href="/FitZone/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
