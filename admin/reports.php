<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Reports';

// Monthly Income
$monthlyIncome = $conn->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'Paid' AND MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())")->fetchColumn();
$totalIncome = $conn->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'Paid'")->fetchColumn();
$totalPayments = $conn->query("SELECT COUNT(*) FROM payments WHERE payment_status = 'Paid' AND MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())")->fetchColumn();

// Active Members
$activeMembers = $conn->query("SELECT m.full_name, m.email, m.phone, p.package_name, m.expiry_date FROM members m LEFT JOIN membership_packages p ON m.package_id = p.package_id WHERE m.status = 'active' ORDER BY m.full_name")->fetchAll();

// Expired Members
$expiredMembers = $conn->query("SELECT m.full_name, m.email, m.phone, p.package_name, m.expiry_date FROM members m LEFT JOIN membership_packages p ON m.package_id = p.package_id WHERE m.status = 'expired' ORDER BY m.full_name")->fetchAll();

// PT Monthly Sessions
$monthlySessions = $conn->query("SELECT COUNT(*) FROM session_bookings WHERE MONTH(session_date) = MONTH(CURDATE()) AND YEAR(session_date) = YEAR(CURDATE())")->fetchColumn();
$approvedSessions = $conn->query("SELECT COUNT(*) FROM session_bookings WHERE booking_status IN ('Approved','Completed') AND MONTH(session_date) = MONTH(CURDATE()) AND YEAR(session_date) = YEAR(CURDATE())")->fetchColumn();
$monthlyPTRevenue = $conn->query("SELECT COALESCE(SUM(t.session_fee), 0) FROM session_bookings sb JOIN trainers t ON sb.trainer_id = t.trainer_id WHERE sb.booking_status IN ('Approved','Completed') AND MONTH(sb.session_date) = MONTH(CURDATE()) AND YEAR(sb.session_date) = YEAR(CURDATE())")->fetchColumn();
$totalPTRevenue = $conn->query("SELECT COALESCE(SUM(t.session_fee), 0) FROM session_bookings sb JOIN trainers t ON sb.trainer_id = t.trainer_id WHERE sb.booking_status IN ('Approved','Completed')")->fetchColumn();

// Trainer Performance
$trainerPerformance = $conn->query("SELECT t.trainer_name, t.specialization, t.session_fee, COUNT(sb.booking_id) as total_sessions, COALESCE(SUM(CASE WHEN sb.booking_status IN ('Approved','Completed') THEN t.session_fee ELSE 0 END), 0) as revenue FROM trainers t LEFT JOIN session_bookings sb ON t.trainer_id = sb.trainer_id GROUP BY t.trainer_id ORDER BY total_sessions DESC")->fetchAll();

require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header">
            <h1><i class="fas fa-chart-bar"></i> Reports</h1>
            <p>View gym performance reports</p>
        </div>

        <!-- Monthly Income Report -->
        <div class="report-section">
            <h3><i class="fas fa-money-bill-wave"></i> Monthly Income Report (<?php echo date('F Y'); ?>)</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-info"><h3>RM <?php echo number_format($monthlyIncome, 2); ?></h3><p>This Month's Income</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-coins"></i></div>
                    <div class="stat-info"><h3>RM <?php echo number_format($totalIncome, 2); ?></h3><p>Total Income (All Time)</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-receipt"></i></div>
                    <div class="stat-info"><h3><?php echo $totalPayments; ?></h3><p>Payments This Month</p></div>
                </div>
            </div>
            <p style="color:var(--text-muted);font-size:0.85rem;"><i class="fas fa-info-circle"></i> Total Monthly Income = SUM(payment_amount) WHERE status = 'Paid'</p>
        </div>

        <!-- PT Session Report -->
        <div class="report-section">
            <h3><i class="fas fa-dumbbell"></i> Monthly PT Session Report (<?php echo date('F Y'); ?>)</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-info"><h3><?php echo $monthlySessions; ?></h3><p>Total Sessions Booked</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-check-double"></i></div>
                    <div class="stat-info"><h3><?php echo $approvedSessions; ?></h3><p>Approved / Completed</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-coins"></i></div>
                    <div class="stat-info"><h3>RM <?php echo number_format($monthlyPTRevenue, 2); ?></h3><p>Monthly PT Revenue</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-wallet"></i></div>
                    <div class="stat-info"><h3>RM <?php echo number_format($totalPTRevenue, 2); ?></h3><p>Total PT Revenue</p></div>
                </div>
            </div>
            <p style="color:var(--text-muted);font-size:0.85rem;"><i class="fas fa-info-circle"></i> Total PT Revenue = Number of Sessions × Session Fee</p>
        </div>

        <!-- Trainer Performance Report -->
        <div class="report-section">
            <h3><i class="fas fa-user-tie" style="color:var(--accent)"></i> Trainer Performance Report</h3>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Trainer</th><th>Specialization</th><th>Fee/Session (RM)</th><th>Total Sessions</th><th>Revenue (RM)</th></tr></thead>
                    <tbody>
                        <?php if (empty($trainerPerformance)): ?>
                            <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No trainers found.</td></tr>
                        <?php else: foreach ($trainerPerformance as $t): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($t['trainer_name']); ?></td>
                                <td><?php echo htmlspecialchars($t['specialization']); ?></td>
                                <td><?php echo number_format($t['session_fee'], 2); ?></td>
                                <td><?php echo $t['total_sessions']; ?></td>
                                <td><?php echo number_format($t['revenue'], 2); ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Active Members Report -->
        <div class="report-section">
            <h3><i class="fas fa-user-check" style="color:var(--success)"></i> Active Membership Report (<?php echo count($activeMembers); ?> members)</h3>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Package</th><th>Expiry</th></tr></thead>
                    <tbody>
                        <?php if (empty($activeMembers)): ?>
                            <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No active members.</td></tr>
                        <?php else: foreach ($activeMembers as $m): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($m['email']); ?></td>
                                <td><?php echo htmlspecialchars($m['phone']); ?></td>
                                <td><?php echo htmlspecialchars($m['package_name'] ?? 'None'); ?></td>
                                <td><?php echo $m['expiry_date'] ?? '-'; ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Expired Members Report -->
        <div class="report-section">
            <h3><i class="fas fa-user-times" style="color:var(--danger)"></i> Expired Membership Report (<?php echo count($expiredMembers); ?> members)</h3>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Package</th><th>Expired On</th></tr></thead>
                    <tbody>
                        <?php if (empty($expiredMembers)): ?>
                            <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No expired members.</td></tr>
                        <?php else: foreach ($expiredMembers as $m): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($m['email']); ?></td>
                                <td><?php echo htmlspecialchars($m['phone']); ?></td>
                                <td><?php echo htmlspecialchars($m['package_name'] ?? 'None'); ?></td>
                                <td><?php echo $m['expiry_date'] ?? '-'; ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
