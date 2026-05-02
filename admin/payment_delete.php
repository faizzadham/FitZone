<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Delete Payment';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT py.*, m.full_name FROM payments py JOIN members m ON py.member_id = m.member_id WHERE py.payment_id = ?");
$stmt->execute([$id]);
$pay = $stmt->fetch();
if (!$pay) { header("Location: payments.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->prepare("DELETE FROM payments WHERE payment_id = ?")->execute([$id]);
    header("Location: payments.php?msg=Payment deleted successfully");
    exit();
}
require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="confirm-box card fade-in">
            <h2><i class="fas fa-exclamation-triangle"></i> Delete Payment</h2>
            <p>Delete payment of <strong>RM <?php echo number_format($pay['amount'], 2); ?></strong> by <strong><?php echo htmlspecialchars($pay['full_name']); ?></strong>?</p>
            <form method="POST">
                <div class="btn-group" style="justify-content:center;">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Yes, Delete</button>
                    <a href="payments.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
