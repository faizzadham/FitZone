<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Manage Payments';
$payments = $conn->query("SELECT py.*, m.full_name FROM payments py JOIN members m ON py.member_id = m.member_id ORDER BY py.payment_id DESC")->fetchAll();
require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header">
            <h1>Payment Management</h1>
            <p>View and manage all payment records</p>
        </div>
        <div style="margin-bottom:1.5rem;">
            <a href="payment_add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Payment</a>
        </div>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>#</th><th>Member</th><th>Date</th><th>Amount (RM)</th><th>Method</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr><td colspan="7" style="text-align:center;color:var(--text-muted);">No payments found.</td></tr>
                    <?php else: foreach ($payments as $p): ?>
                        <tr>
                            <td><?php echo $p['payment_id']; ?></td>
                            <td><?php echo htmlspecialchars($p['full_name']); ?></td>
                            <td><?php echo $p['payment_date']; ?></td>
                            <td><?php echo number_format($p['amount'], 2); ?></td>
                            <td><?php echo $p['payment_method']; ?></td>
                            <td><span class="badge <?php echo $p['payment_status']==='Paid'?'badge-success':($p['payment_status']==='Pending'?'badge-warning':'badge-danger'); ?>"><?php echo $p['payment_status']; ?></span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="payment_edit.php?id=<?php echo $p['payment_id']; ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                                    <a href="payment_delete.php?id=<?php echo $p['payment_id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
