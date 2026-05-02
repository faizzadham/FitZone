<?php
require_once '../includes/auth.php';
requireMember();
require_once '../config/db.php';
$pageTitle = 'Book Session';

$stmt = $conn->prepare("SELECT member_id, full_name FROM members WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$member = $stmt->fetch();

$trainers = $conn->query("SELECT * FROM trainers WHERE status = 'Available' ORDER BY trainer_name")->fetchAll();
$preselect = $_GET['trainer'] ?? '';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trainerId = (int)$_POST['trainer_id'];
    $date = $_POST['session_date'] ?? '';
    $time = $_POST['session_time'] ?? '';
    $type = $_POST['session_type'] ?? 'Strength';
    $notes = trim($_POST['notes'] ?? '');

    if (!$trainerId || !$date || !$time) {
        $errors[] = 'Please fill in all required fields.';
    } elseif ($date < date('Y-m-d')) {
        $errors[] = 'Session date cannot be in the past.';
    } else {
        // Check double booking
        $check = $conn->prepare("SELECT COUNT(*) FROM session_bookings WHERE trainer_id = ? AND session_date = ? AND session_time = ? AND booking_status IN ('Pending','Approved')");
        $check->execute([$trainerId, $date, $time]);
        if ($check->fetchColumn() > 0) {
            $errors[] = 'This trainer is already booked for that date and time. Please choose another slot.';
        } else {
            $stmt = $conn->prepare("INSERT INTO session_bookings (member_id, trainer_id, session_date, session_time, session_type, booking_status, notes) VALUES (?, ?, ?, ?, ?, 'Pending', ?)");
            $stmt->execute([$member['member_id'], $trainerId, $date, $time, $type, $notes]);
            $success = 'Session booked successfully! Please wait for admin approval.';
        }
    }
}

require_once '../includes/header.php';
?>
<div class="container fade-in">
    <div class="page-header">
        <h1><i class="fas fa-calendar-plus"></i> Book a Training Session</h1>
        <p>Schedule a personal training session with your preferred trainer</p>
    </div>
    <div class="card" style="max-width:600px;">
        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <div style="text-align:center;margin-top:1rem;">
                <a href="timetable.php" class="btn btn-primary"><i class="fas fa-calendar-alt"></i> View My Timetable</a>
                <a href="book_session.php" class="btn btn-secondary"><i class="fas fa-plus"></i> Book Another</a>
            </div>
        <?php else: ?>
            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err); ?></div>
            <?php endforeach; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Member Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($member['full_name']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="trainer_id">Select Trainer *</label>
                    <select id="trainer_id" name="trainer_id" required>
                        <option value="">-- Choose Trainer --</option>
                        <?php foreach ($trainers as $t): ?>
                            <option value="<?php echo $t['trainer_id']; ?>" <?php echo $preselect==$t['trainer_id']?'selected':''; ?>>
                                <?php echo htmlspecialchars($t['trainer_name']); ?> — <?php echo $t['specialization']; ?> (RM<?php echo number_format($t['session_fee'],2); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="session_date">Session Date *</label>
                    <input type="date" id="session_date" name="session_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="session_time">Time Slot *</label>
                    <select id="session_time" name="session_time" required>
                        <option value="">-- Choose Time --</option>
                        <?php foreach (['8:00 AM','9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM','6:00 PM','7:00 PM','8:00 PM'] as $ts): ?>
                            <option value="<?php echo $ts; ?>"><?php echo $ts; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="session_type">Session Type *</label>
                    <select id="session_type" name="session_type" required>
                        <option value="Strength">Strength</option>
                        <option value="Cardio">Cardio</option>
                        <option value="Weight Loss">Weight Loss</option>
                        <option value="Rehab">Rehab</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="notes">Additional Notes (optional)</label>
                    <textarea id="notes" name="notes" placeholder="Any special requests or health conditions..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"><i class="fas fa-paper-plane"></i> Submit Booking</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
