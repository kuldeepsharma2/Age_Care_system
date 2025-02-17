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
    <h2 class="text-center">Your Lab Reports</h2>
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