<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Delete Trainer';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM trainers WHERE trainer_id = ?");
$stmt->execute([$id]);
$trainer = $stmt->fetch();
if (!$trainer) { header("Location: trainers.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->prepare("DELETE FROM trainers WHERE trainer_id = ?")->execute([$id]);
    header("Location: trainers.php?msg=Trainer deleted successfully");
    exit();
}
require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="confirm-box card fade-in">
            <h2><i class="fas fa-exclamation-triangle"></i> Delete Trainer</h2>
            <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($trainer['trainer_name']); ?></strong>?<br>All associated bookings will also be removed.</p>
            <form method="POST">
                <div class="btn-group" style="justify-content:center;">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Yes, Delete</button>
                    <a href="trainers.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
