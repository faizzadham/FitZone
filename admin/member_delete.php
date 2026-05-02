<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Delete Member';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM members WHERE member_id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch();
if (!$member) { header("Location: members.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete member and associated user account
    $conn->prepare("DELETE FROM users WHERE user_id = ?")->execute([$member['user_id']]);
    header("Location: members.php?msg=Member deleted successfully");
    exit();
}

require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="confirm-box card fade-in">
            <h2><i class="fas fa-exclamation-triangle"></i> Delete Member</h2>
            <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($member['full_name']); ?></strong>?<br>This action cannot be undone.</p>
            <form method="POST">
                <div class="btn-group" style="justify-content:center;">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Yes, Delete</button>
                    <a href="members.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
