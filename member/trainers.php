<?php
require_once '../includes/auth.php';
requireMember();
require_once '../config/db.php';
$pageTitle = 'Personal Trainers';
$trainers = $conn->query("SELECT * FROM trainers ORDER BY trainer_name")->fetchAll();
require_once '../includes/header.php';
?>
<div class="container fade-in">
    <div class="page-header">
        <h1><i class="fas fa-user-tie"></i> Our Personal Trainers</h1>
        <p>Meet our certified trainers and book a session</p>
    </div>
    <div class="features-grid">
        <?php foreach ($trainers as $t): ?>
            <div class="card trainer-card">
                <div style="text-align:center;margin-bottom:1rem;">
                    <div style="width:70px;height:70px;border-radius:50%;background:var(--gradient);display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;font-size:1.5rem;color:#fff;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($t['trainer_name']); ?></h3>
                    <span class="badge badge-info"><?php echo htmlspecialchars($t['specialization']); ?></span>
                </div>
                <div style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:1rem;">
                    <p><i class="fas fa-calendar-day" style="width:20px;color:var(--accent);"></i> <?php echo htmlspecialchars($t['available_days']); ?></p>
                    <p style="margin-top:0.4rem;"><i class="fas fa-clock" style="width:20px;color:var(--accent);"></i> <?php echo htmlspecialchars($t['available_time']); ?></p>
                    <p style="margin-top:0.4rem;"><i class="fas fa-phone" style="width:20px;color:var(--accent);"></i> <?php echo htmlspecialchars($t['contact_number']); ?></p>
                    <p style="margin-top:0.4rem;"><i class="fas fa-tag" style="width:20px;color:var(--accent);"></i> RM <?php echo number_format($t['session_fee'], 2); ?> / session</p>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span class="badge <?php echo $t['status']==='Available'?'badge-success':'badge-warning'; ?>"><?php echo $t['status']; ?></span>
                    <?php if ($t['status'] === 'Available'): ?>
                        <a href="book_session.php?trainer=<?php echo $t['trainer_id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-calendar-plus"></i> Book</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
