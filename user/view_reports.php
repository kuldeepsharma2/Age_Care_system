<?php
session_start();
include '../database.php';

// Restrict access to users only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Fetch the patient_id for the logged-in user
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id = ?");
$stmt->execute([$userId]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if ($patient) {
    // Fetch reports based on the patient's ID
    $stmt = $pdo->prepare("SELECT * FROM reports WHERE patient_id = ?");
    $stmt->execute([$patient['id']]);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $reports = [];
}
?>

<?php include '../includes/header_user.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">Your Lab Reports</h2>

    <?php if (count($reports) > 0): ?>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Report Type</th>
                <th>Findings</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
            <tr>
                <td><?php echo $report['report_type']; ?></td>
                <td><?php echo $report['findings']; ?></td>
                <td><?php echo $report['created_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No reports available.</p>
    <?php endif; ?>
</div>
</body>

</html>