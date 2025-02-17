<?php
session_start();
include '../database.php';

// Restrict access to staff only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit();
}

$staffId = $_SESSION['user_id'];

// Fetch assigned patients for dropdown
$stmt = $pdo->prepare("SELECT patients.id, users.full_name, users.email 
                       FROM patients 
                       JOIN users ON patients.user_id = users.id 
                       WHERE patients.assigned_staff = ?");
$stmt->execute([$staffId]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Submit treatment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientId = $_POST['patient_id'];
    $disease = $_POST['disease'];
    $medicine = $_POST['medicine'];
    $treatment = $_POST['treatment'];

    $stmt = $pdo->prepare("INSERT INTO treatments (patient_id, disease, medicine, treatment_details, prescribed_by) 
                           VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$patientId, $disease, $medicine, $treatment, $staffId])) {
        $_SESSION['success'] = "Treatment added successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to add treatment!";
    }
}

// Fetch treatment summary for assigned patients
$stmt = $pdo->prepare("SELECT t.id, u.full_name AS patient_name, t.disease, t.medicine, t.treatment_details, t.created_at 
                       FROM treatments t
                       JOIN patients p ON t.patient_id = p.id
                       JOIN users u ON p.user_id = u.id
                       WHERE p.assigned_staff = ? ORDER BY t.created_at DESC");
$stmt->execute([$staffId]);
$treatments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header_user.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">Log Treatment</h2>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; ?></div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Treatment Form -->
    <form method="POST">
        <div class="mb-3">
            <label>Select Patient</label>
            <select name="patient_id" class="form-control" required>
                <option value="">-- Select a Patient --</option>
                <?php foreach ($patients as $patient): ?>
                <option value="<?php echo $patient['id']; ?>">
                    <?php echo $patient['full_name'] . " (" . $patient['email'] . ")"; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Disease</label>
            <input type="text" name="disease" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Medicine</label>
            <input type="text" name="medicine" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Treatment Details</label>
            <textarea name="treatment" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success w-100">Save Treatment</button>
    </form>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Back</a>

    <!-- Treatment Summary Table -->
    <hr>
    <h3>Treatment Summary</h3>
    <?php if (count($treatments) > 0): ?>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Disease</th>
                <th>Medicine</th>
                <th>Treatment Details</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($treatments as $treatment): ?>
            <tr>
                <td><?php echo $treatment['patient_name']; ?></td>
                <td><?php echo $treatment['disease']; ?></td>
                <td><?php echo $treatment['medicine']; ?></td>
                <td><?php echo $treatment['treatment_details']; ?></td>
                <td><?php echo date("F j, Y, g:i a", strtotime($treatment['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="alert alert-warning mt-3">No treatments recorded yet.</div>
    <?php endif; ?>
</div>
</body>

</html>