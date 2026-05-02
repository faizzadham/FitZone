<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Manage Trainers';
$trainers = $conn->query("SELECT * FROM trainers ORDER BY trainer_id")->fetchAll();
require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header">
            <h1>Personal Trainer Management</h1>
            <p>Manage gym personal trainers</p>
        </div>
        <div style="margin-bottom:1.5rem;">
            <a href="trainer_add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Trainer</a>
        </div>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>#</th><th>Name</th><th>Specialization</th><th>Available Days</th><th>Time</th><th>Contact</th><th>Fee (RM)</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($trainers)): ?>
                        <tr><td colspan="9" style="text-align:center;color:var(--text-muted);">No trainers found.</td></tr>
                    <?php else: foreach ($trainers as $t): ?>
                        <tr>
                            <td><?php echo $t['trainer_id']; ?></td>
                            <td><?php echo htmlspecialchars($t['trainer_name']); ?></td>
                            <td><?php echo htmlspecialchars($t['specialization']); ?></td>
                            <td><?php echo htmlspecialchars($t['available_days']); ?></td>
                            <td><?php echo htmlspecialchars($t['available_time']); ?></td>
                            <td><?php echo htmlspecialchars($t['contact_number']); ?></td>
                            <td><?php echo number_format($t['session_fee'], 2); ?></td>
                            <td><span class="badge <?php echo $t['status']==='Available'?'badge-success':'badge-warning'; ?>"><?php echo $t['status']; ?></span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="trainer_edit.php?id=<?php echo $t['trainer_id']; ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                                    <a href="trainer_delete.php?id=<?php echo $t['trainer_id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
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
