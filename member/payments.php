<?php
require_once '../includes/auth.php';
requireMember();
require_once '../config/db.php';
$pageTitle = 'Payment History';

$stmt = $conn->prepare("SELECT member_id FROM members WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$member = $stmt->fetch();

$payments = [];
if ($member) {
    $stmt = $conn->prepare("SELECT * FROM payments WHERE member_id = ? ORDER BY payment_date DESC");
    $stmt->execute([$member['member_id']]);
    $payments = $stmt->fetchAll();
}

require_once '../includes/header.php';
?>
<div class="container fade-in">
    <div class="page-header">
        <h1><i class="fas fa-credit-card"></i> Payment History</h1>
        <p>View all your payment records</p>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>#</th><th>Date</th><th>Amount (RM)</th><th>Method</th><th>Status</th></tr></thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No payment records found.</td></tr>
                <?php else: foreach ($payments as $i => $p): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
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
<?php require_once '../includes/footer.php'; ?>
