<?php
require_once '../includes/auth.php';
requireMember();
require_once '../config/db.php';
$pageTitle = 'My Profile';

$stmt = $conn->prepare("SELECT * FROM members WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$member = $stmt->fetch();
$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = $_POST['gender'] ?? $member['gender'];

    if (empty($fullName) || empty($email)) {
        $errors[] = 'Name and email are required.';
    } else {
        $stmt = $conn->prepare("UPDATE members SET full_name=?, email=?, phone=?, gender=? WHERE member_id=?");
        $stmt->execute([$fullName, $email, $phone, $gender, $member['member_id']]);
        // Also update users table email
        $conn->prepare("UPDATE users SET email=? WHERE user_id=?")->execute([$email, $_SESSION['user_id']]);
        $success = 'Profile updated successfully!';
        // Refresh member data
        $stmt = $conn->prepare("SELECT * FROM members WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $member = $stmt->fetch();
    }
}

require_once '../includes/header.php';
?>
<div class="container fade-in">
    <div class="page-header">
        <h1><i class="fas fa-user"></i> My Profile</h1>
        <p>View and update your personal information</p>
    </div>
    <div class="card" style="max-width:600px;">
        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>
        <?php foreach ($errors as $err): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err); ?></div>
        <?php endforeach; ?>
        <form method="POST">
            <div class="form-group"><label for="full_name">Full Name *</label><input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($member['full_name']); ?>"></div>
            <div class="form-group"><label for="email">Email *</label><input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($member['email']); ?>"></div>
            <div class="form-group"><label for="phone">Phone</label><input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>"></div>
            <div class="form-group">
                <label>Gender</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="Male" <?php echo $member['gender']==='Male'?'checked':''; ?>> Male</label>
                    <label><input type="radio" name="gender" value="Female" <?php echo $member['gender']==='Female'?'checked':''; ?>> Female</label>
                    <label><input type="radio" name="gender" value="Other" <?php echo $member['gender']==='Other'?'checked':''; ?>> Other</label>
                </div>
            </div>
            <div class="form-group"><label>Member Since</label><input type="text" value="<?php echo $member['join_date']; ?>" disabled></div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Profile</button>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
