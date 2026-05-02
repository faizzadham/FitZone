<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Manage Packages';
$packages = $conn->query("SELECT * FROM membership_packages ORDER BY package_id")->fetchAll();
require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header">
            <h1>Membership Packages</h1>
            <p>Manage gym membership packages</p>
        </div>
        <div style="margin-bottom:1.5rem;">
            <a href="package_add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Package</a>
        </div>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>#</th><th>Package Name</th><th>Duration</th><th>Price (RM)</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($packages)): ?>
                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No packages found.</td></tr>
                    <?php else: foreach ($packages as $p): ?>
                        <tr>
                            <td><?php echo $p['package_id']; ?></td>
                            <td><?php echo htmlspecialchars($p['package_name']); ?></td>
                            <td><?php echo $p['duration']; ?> Month<?php echo $p['duration']>1?'s':''; ?></td>
                            <td><?php echo number_format($p['price'], 2); ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="package_edit.php?id=<?php echo $p['package_id']; ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                                    <a href="package_delete.php?id=<?php echo $p['package_id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
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
