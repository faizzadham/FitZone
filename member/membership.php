<?php
require_once '../includes/auth.php';
requireMember();
require_once '../config/db.php';
$pageTitle = 'Membership Details';

$stmt = $conn->prepare("SELECT m.*, p.package_name, p.duration, p.price FROM members m LEFT JOIN membership_packages p ON m.package_id = p.package_id WHERE m.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$member = $stmt->fetch();

$packages = $conn->query("SELECT * FROM membership_packages")->fetchAll();
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['renew_package'])) {
    $pkgId = (int)$_POST['renew_package'];
    $pkg = $conn->prepare("SELECT * FROM membership_packages WHERE package_id = ?");
    $pkg->execute([$pkgId]);
    $pkgData = $pkg->fetch();

    if ($pkgData) {
        $newExpiry = date('Y-m-d', strtotime("+" . $pkgData['duration'] . " months"));
        $conn->prepare("UPDATE members SET package_id=?, status='active', expiry_date=? WHERE member_id=?")->execute([$pkgId, $newExpiry, $member['member_id']]);
        // Add payment record
        $conn->prepare("INSERT INTO payments (member_id, payment_date, amount, payment_method, payment_status) VALUES (?, CURDATE(), ?, 'Online', 'Paid')")->execute([$member['member_id'], $pkgData['price']]);
        $success = 'Membership renewed successfully!';
        // Refresh
        $stmt = $conn->prepare("SELECT m.*, p.package_name, p.duration, p.price FROM members m LEFT JOIN membership_packages p ON m.package_id = p.package_id WHERE m.user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $member = $stmt->fetch();
    }
}

require_once '../includes/header.php';
?>
<div class="container fade-in">
    <div class="page-header">
        <h1><i class="fas fa-box"></i> Membership Details</h1>
        <p>View your current membership and renew</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card" style="max-width:600px;margin-bottom:2rem;">
        <h3 style="margin-bottom:1rem;">Current Membership</h3>
        <div class="stats-grid" style="grid-template-columns:1fr 1fr;">
            <div><p style="color:var(--text-muted);font-size:0.85rem;">Package</p><p style="font-size:1.1rem;font-weight:600;"><?php echo htmlspecialchars($member['package_name'] ?? 'None'); ?></p></div>
            <div><p style="color:var(--text-muted);font-size:0.85rem;">Status</p><p><span class="badge <?php echo $member['status']==='active'?'badge-success':'badge-danger'; ?>"><?php echo ucfirst($member['status']); ?></span></p></div>
            <div><p style="color:var(--text-muted);font-size:0.85rem;">Duration</p><p style="font-size:1.1rem;font-weight:600;"><?php echo ($member['duration'] ?? '-') . ' Month(s)'; ?></p></div>
            <div><p style="color:var(--text-muted);font-size:0.85rem;">Expires</p><p style="font-size:1.1rem;font-weight:600;"><?php echo $member['expiry_date'] ?? 'N/A'; ?></p></div>
        </div>
    </div>

    <div class="card" style="max-width:600px;">
        <h3 style="margin-bottom:1rem;">Renew / Change Package</h3>
        <div class="features-grid" style="grid-template-columns:1fr;">
            <?php foreach ($packages as $p): ?>
                <div class="card" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
                    <div>
                        <h4><?php echo htmlspecialchars($p['package_name']); ?></h4>
                        <p style="color:var(--text-secondary);font-size:0.9rem;"><?php echo $p['duration']; ?> Month<?php echo $p['duration']>1?'s':''; ?> — <strong>RM <?php echo number_format($p['price'], 2); ?></strong></p>
                    </div>
                    <form method="POST" style="margin:0;">
                        <button type="submit" name="renew_package" value="<?php echo $p['package_id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-sync-alt"></i> Select</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
