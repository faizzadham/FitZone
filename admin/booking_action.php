<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';

$id = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? '';

if (in_array($action, ['Approved', 'Rejected', 'Completed'])) {
    $stmt = $conn->prepare("UPDATE session_bookings SET booking_status = ? WHERE booking_id = ?");
    $stmt->execute([$action, $id]);
    header("Location: bookings.php?msg=Booking " . strtolower($action) . " successfully");
} else {
    header("Location: bookings.php");
}
exit();
?>
