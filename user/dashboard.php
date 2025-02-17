<?php
session_start();
include '../database.php';

// Restrict access to users only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Fetch user reports
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM reports WHERE patient_id = ?");
$stmt->execute([$userId]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header_user.php'; ?>
<div class="container mt-5">
    <div class="mt-3 mb-5" style="text-align:center;">
        <a href="schedule_visit.php" class="btn btn-primary mt-4">Schedule
            Visit</a>
        <a href="view_reports.php" class="btn btn-danger mt-4">View Reports</a>

        <a href="user_treatments.php" class="btn btn-success mt-4">View Treatment</a>
    </div>
    <h2 class="text-center">User Dashboard</h2>
    <h4 class="mt-4">Your Medical Reports</h4>
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
</div>
</body>

</html>