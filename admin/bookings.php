<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Manage Bookings';

$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT sb.*, m.full_name, t.trainer_name FROM session_bookings sb JOIN members m ON sb.member_id = m.member_id JOIN trainers t ON sb.trainer_id = t.trainer_id WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (m.full_name LIKE ? OR t.trainer_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($statusFilter) {
    $sql .= " AND sb.booking_status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY sb.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header">
            <h1>Session Booking Management</h1>
            <p>View, approve, reject, and manage all PT bookings</p>
        </div>

        <div class="search-bar">
            <form method="GET" style="display:flex;gap:0.75rem;flex:1;flex-wrap:wrap;">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by member or trainer name...">
                <select name="status" style="padding:0.7rem 1rem;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--text-primary);font-size:0.9rem;">
                    <option value="">All Status</option>
                    <?php foreach (['Pending','Approved','Rejected','Cancelled','Completed'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $statusFilter===$s?'selected':''; ?>><?php echo $s; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                <?php if ($search || $statusFilter): ?><a href="bookings.php" class="btn btn-secondary">Clear</a><?php endif; ?>
            </form>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead><tr><th>#</th><th>Member</th><th>Trainer</th><th>Date</th><th>Time</th><th>Type</th><th>Status</th><th>Notes</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr><td colspan="9" style="text-align:center;color:var(--text-muted);">No bookings found.</td></tr>
                    <?php else: foreach ($bookings as $b): ?>
                        <tr>
                            <td><?php echo $b['booking_id']; ?></td>
                            <td><?php echo htmlspecialchars($b['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($b['trainer_name']); ?></td>
                            <td><?php echo $b['session_date']; ?></td>
                            <td><?php echo $b['session_time']; ?></td>
                            <td><?php echo $b['session_type']; ?></td>
                            <td><span class="badge <?php
                                echo match($b['booking_status']) {
                                    'Approved' => 'badge-success',
                                    'Pending' => 'badge-warning',
                                    'Completed' => 'badge-info',
                                    default => 'badge-danger'
                                }; ?>"><?php echo $b['booking_status']; ?></span></td>
                            <td><?php echo htmlspecialchars(substr($b['notes'] ?? '', 0, 30)); ?></td>
                            <td>
                                <div class="btn-group">
                                    <?php if ($b['booking_status'] === 'Pending'): ?>
                                        <a href="booking_action.php?id=<?php echo $b['booking_id']; ?>&action=Approved" class="btn btn-sm btn-success"><i class="fas fa-check"></i></a>
                                        <a href="booking_action.php?id=<?php echo $b['booking_id']; ?>&action=Rejected" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                                    <?php endif; ?>
                                    <a href="booking_edit.php?id=<?php echo $b['booking_id']; ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                                    <a href="booking_delete.php?id=<?php echo $b['booking_id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
