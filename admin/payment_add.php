<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Add Payment';
$members = $conn->query("SELECT member_id, full_name FROM members ORDER BY full_name")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberId = (int)($_POST['member_id'] ?? 0);
    $paymentDate = $_POST['payment_date'] ?? date('Y-m-d');
    $amount = (float)($_POST['amount'] ?? 0);
    $method = $_POST['payment_method'] ?? 'Cash';
    $status = $_POST['payment_status'] ?? 'Pending';

    if ($memberId <= 0 || $amount <= 0) {
        $errors[] = 'Please select a member and enter a valid amount.';
    } else {
        $stmt = $conn->prepare("INSERT INTO payments (member_id, payment_date, amount, payment_method, payment_status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$memberId, $paymentDate, $amount, $method, $status]);
        header("Location: payments.php?msg=Payment added successfully");
        exit();
    }
}
require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header"><h1>Add Payment</h1></div>
        <div class="card" style="max-width:500px;">
            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err); ?></div>
            <?php endforeach; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="member_id">Member *</label>
                    <select id="member_id" name="member_id" required>
                        <option value="">-- Select Member --</option>
                        <?php foreach ($members as $m): ?>
                            <option value="<?php echo $m['member_id']; ?>"><?php echo htmlspecialchars($m['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label for="payment_date">Payment Date</label><input type="date" id="payment_date" name="payment_date" value="<?php echo date('Y-m-d'); ?>"></div>
                <div class="form-group"><label for="amount">Amount (RM) *</label><input type="number" id="amount" name="amount" step="0.01" min="0.01" required></div>
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select id="payment_method" name="payment_method">
                        <option value="Cash">Cash</option>
                        <option value="Card">Card</option>
                        <option value="Online">Online</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="payment_status">Status</label>
                    <select id="payment_status" name="payment_status">
                        <option value="Paid">Paid</option>
                        <option value="Pending" selected>Pending</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="btn-group" style="margin-top:1rem;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                    <a href="payments.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
