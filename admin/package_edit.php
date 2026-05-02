<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Edit Package';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM membership_packages WHERE package_id = ?");
$stmt->execute([$id]);
$pkg = $stmt->fetch();
if (!$pkg) { header("Location: packages.php"); exit(); }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['package_name'] ?? '');
    $duration = (int)($_POST['duration'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);

    if (empty($name) || $duration <= 0 || $price <= 0) {
        $errors[] = 'All fields are required with valid values.';
    } else {
        $stmt = $conn->prepare("UPDATE membership_packages SET package_name=?, duration=?, price=? WHERE package_id=?");
        $stmt->execute([$name, $duration, $price, $id]);
        header("Location: packages.php?msg=Package updated successfully");
        exit();
    }
}
require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header"><h1>Edit Package</h1></div>
        <div class="card" style="max-width:500px;">
            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err); ?></div>
            <?php endforeach; ?>
            <form method="POST">
                <div class="form-group"><label for="package_name">Package Name *</label><input type="text" id="package_name" name="package_name" required value="<?php echo htmlspecialchars($pkg['package_name']); ?>"></div>
                <div class="form-group"><label for="duration">Duration (months) *</label><input type="number" id="duration" name="duration" min="1" required value="<?php echo $pkg['duration']; ?>"></div>
                <div class="form-group"><label for="price">Price (RM) *</label><input type="number" id="price" name="price" step="0.01" min="0.01" required value="<?php echo $pkg['price']; ?>"></div>
                <div class="btn-group" style="margin-top:1rem;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <a href="packages.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
