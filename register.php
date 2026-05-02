<?php
require_once 'includes/auth.php';
require_once 'config/db.php';

$pageTitle = 'Register';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = $_POST['gender'] ?? 'Male';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    if (empty($fullName) || empty($email) || empty($username) || empty($password)) {
        $errors[] = 'All required fields must be filled.';
    }
    if ($password !== $confirmPass) {
        $errors[] = 'Passwords do not match.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (empty($errors)) {
        // Check if username or email exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already exists.';
        } else {
            $hashedPass = password_hash($password, PASSWORD_DEFAULT);
            $conn->beginTransaction();
            try {
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'member')");
                $stmt->execute([$username, $email, $hashedPass]);
                $userId = $conn->lastInsertId();

                $stmt = $conn->prepare("INSERT INTO members (user_id, full_name, email, phone, gender, join_date) VALUES (?, ?, ?, ?, ?, CURDATE())");
                $stmt->execute([$userId, $fullName, $email, $phone, $gender]);

                $conn->commit();
                $success = 'Registration successful! You can now login.';
            } catch (Exception $e) {
                $conn->rollBack();
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card fade-in">
        <h2><i class="fas fa-user-plus"></i> Register</h2>
        <p class="subtitle">Create your FitZone member account</p>

        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <div class="auth-footer"><a href="login.php">Click here to login</a></div>
        <?php else: ?>
            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err); ?></div>
            <?php endforeach; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($fullName ?? ''); ?>" placeholder="Enter your full name">
                </div>
                <div class="form-group">
                    <label for="reg_email">Email *</label>
                    <input type="email" id="reg_email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>" placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" placeholder="e.g. 012-3456789">
                </div>
                <div class="form-group">
                    <label>Gender *</label>
                    <div class="radio-group">
                        <label><input type="radio" name="gender" value="Male" <?php echo ($gender ?? 'Male') === 'Male' ? 'checked' : ''; ?>> Male</label>
                        <label><input type="radio" name="gender" value="Female" <?php echo ($gender ?? '') === 'Female' ? 'checked' : ''; ?>> Female</label>
                        <label><input type="radio" name="gender" value="Other" <?php echo ($gender ?? '') === 'Other' ? 'checked' : ''; ?>> Other</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="reg_username">Username *</label>
                    <input type="text" id="reg_username" name="username" required value="<?php echo htmlspecialchars($username ?? ''); ?>" placeholder="Choose a username">
                </div>
                <div class="form-group">
                    <label for="reg_password">Password *</label>
                    <input type="password" id="reg_password" name="password" required placeholder="At least 6 characters">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Create Account</button>
            </form>
            <div class="auth-footer">Already have an account? <a href="login.php">Login here</a></div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
