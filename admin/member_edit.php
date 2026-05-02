<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Edit Member';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM members WHERE member_id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch();
if (!$member) { header("Location: members.php"); exit(); }

$packages = $conn->query("SELECT * FROM membership_packages")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = $_POST['gender'] ?? 'Male';
    $packageId = $_POST['package_id'] ?? null;
    $status = $_POST['status'] ?? 'active';
    $expiryDate = $_POST['expiry_date'] ?? null;

    if (empty($fullName) || empty($email)) {
        $errors[] = 'Name and email are required.';
    }

    if (empty($errors)) {
        $pkgId = $packageId ?: null;
        $expDate = $expiryDate ?: null;
        $stmt = $conn->prepare("UPDATE members SET full_name=?, email=?, phone=?, gender=?, package_id=?, status=?, expiry_date=? WHERE member_id=?");
        $stmt->execute([$fullName, $email, $phone, $gender, $pkgId, $status, $expDate, $id]);
        header("Location: members.php?msg=Member updated successfully");
        exit();
    }
}

require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header">
            <h1>Edit Member</h1>
            <p>Update member information</p>
        </div>
        <div class="card" style="max-width:700px;">
            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err); ?></div>
            <?php endforeach; ?>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($member['full_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($member['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <div class="radio-group">
                            <label><input type="radio" name="gender" value="Male" <?php echo $member['gender']==='Male'?'checked':''; ?>> Male</label>
                            <label><input type="radio" name="gender" value="Female" <?php echo $member['gender']==='Female'?'checked':''; ?>> Female</label>
                            <label><input type="radio" name="gender" value="Other" <?php echo $member['gender']==='Other'?'checked':''; ?>> Other</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="package_id">Package</label>
                        <select id="package_id" name="package_id">
                            <option value="">-- No Package --</option>
                            <?php foreach ($packages as $p): ?>
                                <option value="<?php echo $p['package_id']; ?>" <?php echo $member['package_id']==$p['package_id']?'selected':''; ?>><?php echo htmlspecialchars($p['package_name']); ?> (RM<?php echo $p['price']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="active" <?php echo $member['status']==='active'?'selected':''; ?>>Active</option>
                            <option value="expired" <?php echo $member['status']==='expired'?'selected':''; ?>>Expired</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="date" id="expiry_date" name="expiry_date" value="<?php echo $member['expiry_date']; ?>">
                    </div>
                </div>
                <div class="btn-group" style="margin-top:1rem;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Member</button>
                    <a href="members.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
