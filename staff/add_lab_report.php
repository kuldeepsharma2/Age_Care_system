<?php
session_start();
include '../database.php';

// Restrict access to staff or admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../login.php");
    exit();
}

// Fetch patients - Join users table to get the patient's full name
$stmt = $pdo->query("SELECT p.id, u.full_name 
                     FROM patients p
                     JOIN users u ON p.user_id = u.id");
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientId = $_POST['patient_id'];
    $reportType = $_POST['report_type'];
    $findings = $_POST['findings'];

    $stmt = $pdo->prepare("INSERT INTO reports (patient_id, report_type, findings) VALUES (?, ?, ?)");
    if ($stmt->execute([$patientId, $reportType, $findings])) {
        $_SESSION['success'] = "Lab Report added successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to add Lab Report!";
    }
}
?>

<?php include '../includes/header_user.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">Add Lab Report</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Select Patient</label>
            <select name="patient_id" class="form-control" required>
                <?php foreach ($patients as $patient): ?>
                <option value="<?php echo $patient['id']; ?>"><?php echo $patient['full_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Report Type</label>
            <input type="text" name="report_type" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Findings</label>
            <textarea name="findings" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success w-100">Submit Report</button>
    </form>

</div>
</body>

</html>