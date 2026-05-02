<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Delete Package';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM membership_packages WHERE package_id = ?");
$stmt->execute([$id]);
$pkg = $stmt->fetch();
if (!$pkg) { header("Location: packages.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->prepare("DELETE FROM membership_packages WHERE package_id = ?")->execute([$id]);
    header("Location: packages.php?msg=Package deleted successfully");
    exit();
}
require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="confirm-box card fade-in">
            <h2><i class="fas fa-exclamation-triangle"></i> Delete Package</h2>
            <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($pkg['package_name']); ?></strong>?</p>
            <form method="POST">
                <div class="btn-group" style="justify-content:center;">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Yes, Delete</button>
                    <a href="packages.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
