<?php
require_once '../includes/auth.php';
requireMember();
require_once '../config/db.php';
$pageTitle = 'My Timetable';

$stmt = $conn->prepare("SELECT member_id FROM members WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$member = $stmt->fetch();

// Handle cancel
if (isset($_GET['cancel'])) {
    $cancelId = (int)$_GET['cancel'];
    $conn->prepare("UPDATE session_bookings SET booking_status = 'Cancelled' WHERE booking_id = ? AND member_id = ? AND booking_status IN ('Pending','Approved')")->execute([$cancelId, $member['member_id']]);
    header("Location: timetable.php?msg=Session cancelled successfully");
    exit();
}

// Upcoming sessions (today and future)
$upcoming = $conn->prepare("SELECT sb.*, t.trainer_name, t.session_fee FROM session_bookings sb JOIN trainers t ON sb.trainer_id = t.trainer_id WHERE sb.member_id = ? AND sb.session_date >= CURDATE() AND sb.booking_status IN ('Pending','Approved') ORDER BY sb.session_date ASC, sb.session_time ASC");
$upcoming->execute([$member['member_id']]);
$upcomingSessions = $upcoming->fetchAll();

// Past / history
$history = $conn->prepare("SELECT sb.*, t.trainer_name, t.session_fee FROM session_bookings sb JOIN trainers t ON sb.trainer_id = t.trainer_id WHERE sb.member_id = ? AND (sb.session_date < CURDATE() OR sb.booking_status IN ('Completed','Cancelled','Rejected')) ORDER BY sb.session_date DESC");
$history->execute([$member['member_id']]);
$pastSessions = $history->fetchAll();

require_once '../includes/header.php';
?>
<div class="container fade-in">
    <div class="page-header">
        <h1><i class="fas fa-calendar-alt"></i> My Timetable</h1>
        <p>View your upcoming and past training sessions</p>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <div style="margin-bottom:1.5rem;">
        <a href="book_session.php" class="btn btn-primary"><i class="fas fa-plus"></i> Book New Session</a>
        <a href="trainers.php" class="btn btn-secondary"><i class="fas fa-user-tie"></i> View Trainers</a>
    </div>

    <!-- Upcoming Sessions -->
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <h3><i class="fas fa-clock" style="color:var(--accent);"></i> Upcoming Sessions</h3>
        </div>
        <?php if (empty($upcomingSessions)): ?>
            <p style="text-align:center;color:var(--text-muted);padding:1rem;">No upcoming sessions. <a href="book_session.php" style="color:var(--accent);">Book one now!</a></p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Date</th><th>Time</th><th>Trainer</th><th>Type</th><th>Fee</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($upcomingSessions as $s): ?>
                        <tr>
                            <td><?php echo $s['session_date']; ?></td>
                            <td><?php echo $s['session_time']; ?></td>
                            <td><?php echo htmlspecialchars($s['trainer_name']); ?></td>
                            <td><?php echo $s['session_type']; ?></td>
                            <td>RM <?php echo number_format($s['session_fee'], 2); ?></td>
                            <td><span class="badge <?php echo $s['booking_status']==='Approved'?'badge-success':'badge-warning'; ?>"><?php echo $s['booking_status']; ?></span></td>
                            <td><a href="timetable.php?cancel=<?php echo $s['booking_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this session?');"><i class="fas fa-times"></i> Cancel</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Session History -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-history" style="color:var(--text-muted);"></i> Session History</h3>
        </div>
        <?php if (empty($pastSessions)): ?>
            <p style="text-align:center;color:var(--text-muted);padding:1rem;">No past sessions.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Date</th><th>Time</th><th>Trainer</th><th>Type</th><th>Fee</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($pastSessions as $s): ?>
                        <tr>
                            <td><?php echo $s['session_date']; ?></td>
                            <td><?php echo $s['session_time']; ?></td>
                            <td><?php echo htmlspecialchars($s['trainer_name']); ?></td>
                            <td><?php echo $s['session_type']; ?></td>
                            <td>RM <?php echo number_format($s['session_fee'], 2); ?></td>
                            <td><span class="badge <?php
                                echo match($s['booking_status']) {
                                    'Completed' => 'badge-info',
                                    'Approved' => 'badge-success',
                                    'Cancelled' => 'badge-danger',
                                    'Rejected' => 'badge-danger',
                                    default => 'badge-warning'
                                }; ?>"><?php echo $s['booking_status']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
