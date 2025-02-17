<?php
session_start();
include '../database.php';

// Restrict access to staff only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit();
}

// Fetch assigned patients with user details, including the user's age
$staffId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT p.id, u.full_name AS name, 
           TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) AS age, 
           p.medical_history
    FROM patients p
    JOIN users u ON p.user_id = u.id
    WHERE p.assigned_staff = ?
");
$stmt->execute([$staffId]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header_user.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">Staff Dashboard</h2>
    <div class="mt-3 mb-5 text-center">
        <a href="log_treatment.php" class="btn btn-primary mt-4">Treatment</a>
        <a href="add_lab_report.php" class="btn btn-secondary mt-4">Add Report</a>
        <a href="view_reports.php" class="btn btn-secondary mt-4">View Report</a>

    </div>

    <h4 class="mt-4">Assigned Patients</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Medical History</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($patients as $patient): ?>
            <tr>
                <!-- Display Patient's Name -->
                <td><?php echo htmlspecialchars($patient['name']); ?></td>

                <!-- Display Patient's Age -->
                <td><?php echo $patient['age']; ?></td>

                <!-- Display Patient's Medical History -->
                <td><?php echo htmlspecialchars($patient['medical_history']); ?></td>

                <!-- Button to Log Treatment -->
                <td>
                    <a href="log_treatment.php?patient_id=<?php echo $patient['id']; ?>" class="btn btn-info">
                        Log Treatment
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>

</html>