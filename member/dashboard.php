<?php
require_once '../includes/auth.php';
requireMember();
require_once '../config/db.php';
$pageTitle = 'My Dashboard';

$stmt = $conn->prepare("SELECT m.*, p.package_name, p.price FROM members m LEFT JOIN membership_packages p ON m.package_id = p.package_id WHERE m.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$member = $stmt->fetch();

$recentPayments = [];
$upcomingSessions = [];
if ($member) {
    $stmt = $conn->prepare("SELECT * FROM payments WHERE member_id = ? ORDER BY payment_date DESC LIMIT 5");
    $stmt->execute([$member['member_id']]);
    $recentPayments = $stmt->fetchAll();

    $stmt = $conn->prepare("SELECT sb.*, t.trainer_name FROM session_bookings sb JOIN trainers t ON sb.trainer_id = t.trainer_id WHERE sb.member_id = ? AND sb.session_date >= CURDATE() AND sb.booking_status IN ('Pending','Approved') ORDER BY sb.session_date ASC LIMIT 3");
    $stmt->execute([$member['member_id']]);
    $upcomingSessions = $stmt->fetchAll();
}

require_once '../includes/header.php';
?>
<div class="container fade-in">
    <div class="page-header">
        <h1>Welcome, <?php echo htmlspecialchars($member['full_name'] ?? $_SESSION['username']); ?>!</h1>
        <p>Your membership overview</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-id-card"></i></div>
            <div class="stat-info">
                <h3><?php echo htmlspecialchars($member['package_name'] ?? 'None'); ?></h3>
                <p>Current Package</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon <?php echo $member['status']==='active'?'green':'red'; ?>"><i class="fas fa-<?php echo $member['status']==='active'?'check-circle':'times-circle'; ?>"></i></div>
            <div class="stat-info">
                <h3><?php echo ucfirst($member['status'] ?? 'N/A'); ?></h3>
                <p>Membership Status</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-info">
                <h3><?php echo $member['expiry_date'] ?? 'N/A'; ?></h3>
                <p>Expiry Date</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-dumbbell"></i></div>
            <div class="stat-info">
                <h3><?php echo count($upcomingSessions); ?></h3>
                <p>Upcoming PT Sessions</p>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:2rem;">
        <a href="profile.php" class="btn btn-primary"><i class="fas fa-user"></i> My Profile</a>
        <a href="membership.php" class="btn btn-secondary"><i class="fas fa-box"></i> Membership</a>
        <a href="payments.php" class="btn btn-secondary"><i class="fas fa-credit-card"></i> Payments</a>
        <a href="trainers.php" class="btn btn-secondary"><i class="fas fa-user-tie"></i> Trainers</a>
        <a href="book_session.php" class="btn btn-primary"><i class="fas fa-calendar-plus"></i> Book Session</a>
        <a href="timetable.php" class="btn btn-secondary"><i class="fas fa-calendar-alt"></i> My Timetable</a>
    </div>

    <!-- Upcoming PT Sessions -->
    <?php if (!empty($upcomingSessions)): ?>
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <h3><i class="fas fa-dumbbell" style="color:var(--accent);"></i> Upcoming PT Sessions</h3>
            <a href="timetable.php" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Date</th><th>Time</th><th>Trainer</th><th>Type</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($upcomingSessions as $s): ?>
                    <tr>
                        <td><?php echo $s['session_date']; ?></td>
                        <td><?php echo $s['session_time']; ?></td>
                        <td><?php echo htmlspecialchars($s['trainer_name']); ?></td>
                        <td><?php echo $s['session_type']; ?></td>
                        <td><span class="badge <?php echo $s['booking_status']==='Approved'?'badge-success':'badge-warning'; ?>"><?php echo $s['booking_status']; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Payments -->
    <div class="card">
        <div class="card-header">
            <h3>Recent Payments</h3>
            <a href="payments.php" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Date</th><th>Amount (RM)</th><th>Method</th><th>Status</th></tr></thead>
                <tbody>
                    <?php if (empty($recentPayments)): ?>
                        <tr><td colspan="4" style="text-align:center;color:var(--text-muted);">No payments yet.</td></tr>
                    <?php else: foreach ($recentPayments as $p): ?>
                        <tr>
                            <td><?php echo $p['payment_date']; ?></td>
                            <td><?php echo number_format($p['amount'], 2); ?></td>
                            <td><?php echo $p['payment_method']; ?></td>
                            <td><span class="badge <?php echo $p['payment_status']==='Paid'?'badge-success':($p['payment_status']==='Pending'?'badge-warning':'badge-danger'); ?>"><?php echo $p['payment_status']; ?></span></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
