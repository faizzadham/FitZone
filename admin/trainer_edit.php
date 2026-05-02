<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Edit Trainer';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM trainers WHERE trainer_id = ?");
$stmt->execute([$id]);
$trainer = $stmt->fetch();
if (!$trainer) { header("Location: trainers.php"); exit(); }

$errors = [];
$selectedDays = array_map('trim', explode(',', $trainer['available_days']));

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
        $selectedDays = $_POST['available_days'] ?? [];
    } else {
        $stmt = $conn->prepare("UPDATE trainers SET trainer_name=?, specialization=?, available_days=?, available_time=?, contact_number=?, session_fee=?, status=? WHERE trainer_id=?");
        $stmt->execute([$name, $spec, $days, $time, $contact, $fee, $status, $id]);
        header("Location: trainers.php?msg=Trainer updated successfully");
        exit();
    }
}
require_once '../includes/header.php';
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="page-header"><h1>Edit Trainer</h1></div>
        <div class="card" style="max-width:650px;">
            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($err); ?></div>
            <?php endforeach; ?>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="trainer_name">Trainer Name *</label>
                        <input type="text" id="trainer_name" name="trainer_name" required value="<?php echo htmlspecialchars($trainer['trainer_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="specialization">Specialization *</label>
                        <select id="specialization" name="specialization" required>
                            <?php foreach (['Strength Training','Cardio & HIIT','Weight Loss','Rehabilitation','General Fitness'] as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo $trainer['specialization']===$s?'selected':''; ?>><?php echo $s; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label>Available Days *</label>
                        <div class="checkbox-group">
                            <?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d): ?>
                                <label><input type="checkbox" name="available_days[]" value="<?php echo $d; ?>" <?php echo in_array($d, $selectedDays)?'checked':''; ?>> <?php echo $d; ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="available_time">Available Time *</label>
                        <input type="text" id="available_time" name="available_time" required value="<?php echo htmlspecialchars($trainer['available_time']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($trainer['contact_number']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="session_fee">Session Fee (RM) *</label>
                        <input type="number" id="session_fee" name="session_fee" step="0.01" min="0.01" required value="<?php echo $trainer['session_fee']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="Available" <?php echo $trainer['status']==='Available'?'selected':''; ?>>Available</option>
                            <option value="Busy" <?php echo $trainer['status']==='Busy'?'selected':''; ?>>Busy</option>
                        </select>
                    </div>
                </div>
                <div class="btn-group" style="margin-top:1rem;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <a href="trainers.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
