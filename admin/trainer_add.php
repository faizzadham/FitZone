<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Add Trainer';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['trainer_name'] ?? '');
    $spec = trim($_POST['specialization'] ?? '');
    $days = isset($_POST['available_days']) ? implode(', ', $_POST['available_days']) : '';
    $time = trim($_POST['available_time'] ?? '');
    $contact = trim($_POST['contact_number'] ?? '');
    $fee = (float)($_POST['session_fee'] ?? 50);
    $status = $_POST['status'] ?? 'Available';

    if (empty($name) || empty($spec) || empty($days) || empty($time)) {
        $errors[] = 'All required fields must be filled.';
    } else {
        $stmt = $conn->prepare("INSERT INTO trainers (trainer_name, specialization, available_days, available_time, contact_number, session_fee, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $spec, $days, $time, $contact, $fee, $status]);
        header("Location: trainers.php?msg=Trainer added successfully");
        exit();
    }
}
require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header"><h1>Add Trainer</h1><p>Register a new personal trainer</p></div>
        <div class="card" style="max-width:650px;">
            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err); ?></div>
            <?php endforeach; ?>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="trainer_name">Trainer Name *</label>
                        <input type="text" id="trainer_name" name="trainer_name" required value="<?php echo htmlspecialchars($name ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="specialization">Specialization *</label>
                        <select id="specialization" name="specialization" required>
                            <option value="">-- Select --</option>
                            <option value="Strength Training">Strength Training</option>
                            <option value="Cardio & HIIT">Cardio & HIIT</option>
                            <option value="Weight Loss">Weight Loss</option>
                            <option value="Rehabilitation">Rehabilitation</option>
                            <option value="General Fitness">General Fitness</option>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label>Available Days *</label>
                        <div class="checkbox-group">
                            <?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d): ?>
                                <label><input type="checkbox" name="available_days[]" value="<?php echo $d; ?>"> <?php echo $d; ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="available_time">Available Time *</label>
                        <input type="text" id="available_time" name="available_time" required placeholder="e.g. 8:00 AM - 12:00 PM">
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" placeholder="e.g. 012-3456789">
                    </div>
                    <div class="form-group">
                        <label for="session_fee">Session Fee (RM) *</label>
                        <input type="number" id="session_fee" name="session_fee" step="0.01" min="0.01" value="50.00" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="Available">Available</option>
                            <option value="Busy">Busy</option>
                        </select>
                    </div>
                </div>
                <div class="btn-group" style="margin-top:1rem;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Trainer</button>
                    <a href="trainers.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
