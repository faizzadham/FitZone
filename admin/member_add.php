<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Add Member';
$packages = $conn->query("SELECT * FROM membership_packages")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = $_POST['gender'] ?? 'Male';
    $packageId = $_POST['package_id'] ?? null;
    $joinDate = $_POST['join_date'] ?? date('Y-m-d');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($fullName) || empty($email) || empty($username) || empty($password)) {
        $errors[] = 'All required fields must be filled.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already exists.';
        } else {
            $conn->beginTransaction();
            try {
                $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'member')");
                $stmt->execute([$username, $email, $hashedPass]);
                $userId = $conn->lastInsertId();

                $pkgId = $packageId ?: null;
                $expiryDate = null;
                if ($pkgId) {
                    $pkg = $conn->prepare("SELECT duration FROM membership_packages WHERE package_id = ?");
                    $pkg->execute([$pkgId]);
                    $dur = $pkg->fetchColumn();
                    if ($dur) {
                        $expiryDate = date('Y-m-d', strtotime($joinDate . " + $dur months"));
                    }
                }

                $stmt = $conn->prepare("INSERT INTO members (user_id, full_name, email, phone, gender, join_date, package_id, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $fullName, $email, $phone, $gender, $joinDate, $pkgId, $expiryDate]);
                $conn->commit();
                header("Location: members.php?msg=Member added successfully");
                exit();
            } catch (Exception $e) {
                $conn->rollBack();
                $errors[] = 'Failed to add member.';
            }
        }
    }
}

require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header">
            <h1>Add New Member</h1>
            <p>Create a new member account</p>
        </div>
        <div class="card" style="max-width:700px;">
            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err); ?></div>
            <?php endforeach; ?>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($fullName ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Gender *</label>
                        <div class="radio-group">
                            <label><input type="radio" name="gender" value="Male" checked> Male</label>
                            <label><input type="radio" name="gender" value="Female"> Female</label>
                            <label><input type="radio" name="gender" value="Other"> Other</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="package_id">Package</label>
                        <select id="package_id" name="package_id">
                            <option value="">-- No Package --</option>
                            <?php foreach ($packages as $p): ?>
                                <option value="<?php echo $p['package_id']; ?>"><?php echo htmlspecialchars($p['package_name']); ?> (RM<?php echo $p['price']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="join_date">Join Date</label>
                        <input type="date" id="join_date" name="join_date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($username ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                <div class="btn-group" style="margin-top:1rem;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Member</button>
                    <a href="members.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
