<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Edit Payment';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM payments WHERE payment_id = ?");
$stmt->execute([$id]);
$pay = $stmt->fetch();
if (!$pay) { header("Location: payments.php"); exit(); }

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
        $stmt = $conn->prepare("UPDATE payments SET member_id=?, payment_date=?, amount=?, payment_method=?, payment_status=? WHERE payment_id=?");
        $stmt->execute([$memberId, $paymentDate, $amount, $method, $status, $id]);
        header("Location: payments.php?msg=Payment updated successfully");
        exit();
    }
}
require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header"><h1>Edit Payment</h1></div>
        <div class="card" style="max-width:500px;">
            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err); ?></div>
            <?php endforeach; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="member_id">Member *</label>
                    <select id="member_id" name="member_id" required>
                        <?php foreach ($members as $m): ?>
                            <option value="<?php echo $m['member_id']; ?>" <?php echo $pay['member_id']==$m['member_id']?'selected':''; ?>><?php echo htmlspecialchars($m['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label for="payment_date">Payment Date</label><input type="date" id="payment_date" name="payment_date" value="<?php echo $pay['payment_date']; ?>"></div>
                <div class="form-group"><label for="amount">Amount (RM) *</label><input type="number" id="amount" name="amount" step="0.01" min="0.01" required value="<?php echo $pay['amount']; ?>"></div>
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select id="payment_method" name="payment_method">
                        <?php foreach (['Cash','Card','Online'] as $mt): ?>
                            <option value="<?php echo $mt; ?>" <?php echo $pay['payment_method']===$mt?'selected':''; ?>><?php echo $mt; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="payment_status">Status</label>
                    <select id="payment_status" name="payment_status">
                        <?php foreach (['Paid','Pending','Cancelled'] as $st): ?>
                            <option value="<?php echo $st; ?>" <?php echo $pay['payment_status']===$st?'selected':''; ?>><?php echo $st; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="btn-group" style="margin-top:1rem;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <a href="payments.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
