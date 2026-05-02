<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Delete Booking';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT sb.*, m.full_name, t.trainer_name FROM session_bookings sb JOIN members m ON sb.member_id = m.member_id JOIN trainers t ON sb.trainer_id = t.trainer_id WHERE sb.booking_id = ?");
$stmt->execute([$id]);
$booking = $stmt->fetch();
if (!$booking) { header("Location: bookings.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->prepare("DELETE FROM session_bookings WHERE booking_id = ?")->execute([$id]);
    header("Location: bookings.php?msg=Booking deleted successfully");
    exit();
}
require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="confirm-box card fade-in">
            <h2><i class="fas fa-exclamation-triangle"></i> Delete Booking</h2>
            <p>Delete booking for <strong><?php echo htmlspecialchars($booking['full_name']); ?></strong> with <strong><?php echo htmlspecialchars($booking['trainer_name']); ?></strong> on <?php echo $booking['session_date']; ?>?</p>
            <form method="POST">
                <div class="btn-group" style="justify-content:center;">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Yes, Delete</button>
                    <a href="bookings.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
