<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Manage Members';

$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $conn->prepare("SELECT m.*, p.package_name FROM members m LEFT JOIN membership_packages p ON m.package_id = p.package_id WHERE m.full_name LIKE ? OR m.email LIKE ? OR m.phone LIKE ? ORDER BY m.member_id DESC");
    $like = "%$search%";
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = $conn->query("SELECT m.*, p.package_name FROM members m LEFT JOIN membership_packages p ON m.package_id = p.package_id ORDER BY m.member_id DESC");
}
$members = $stmt->fetchAll();

require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header">
            <h1>Member Management</h1>
            <p>View, search, add, edit, or delete members</p>
        </div>

        <div class="search-bar">
            <form method="GET" style="display:flex;gap:0.75rem;flex:1;flex-wrap:wrap;">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, email, or phone...">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                <?php if ($search): ?><a href="members.php" class="btn btn-secondary">Clear</a><?php endif; ?>
            </form>
            <a href="member_add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Member</a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead><tr><th>#</th><th>Full Name</th><th>Email</th><th>Phone</th><th>Gender</th><th>Package</th><th>Status</th><th>Expiry</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($members)): ?>
                        <tr><td colspan="9" style="text-align:center;color:var(--text-muted);">No members found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($members as $m): ?>
                        <tr>
                            <td><?php echo $m['member_id']; ?></td>
                            <td><?php echo htmlspecialchars($m['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($m['email']); ?></td>
                            <td><?php echo htmlspecialchars($m['phone']); ?></td>
                            <td><?php echo $m['gender']; ?></td>
                            <td><?php echo htmlspecialchars($m['package_name'] ?? 'None'); ?></td>
                            <td><span class="badge <?php echo $m['status'] === 'active' ? 'badge-success' : 'badge-danger'; ?>"><?php echo ucfirst($m['status']); ?></span></td>
                            <td><?php echo $m['expiry_date'] ?? '-'; ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="member_edit.php?id=<?php echo $m['member_id']; ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                                    <a href="member_delete.php?id=<?php echo $m['member_id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
