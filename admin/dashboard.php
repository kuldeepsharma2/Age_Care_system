<?php
session_start();
include '../database.php';

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch total counts
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPatients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$totalReports = $pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn();
$totalTreatments = $pdo->query("SELECT COUNT(*) FROM treatments")->fetchColumn();
?>

<?php include '../includes/header_admin.php'; ?>


<div class="container mt-5">
    <h2 class="text-center">Admin Dashboard</h2>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card p-3 text-center">
                <h5>Total Users</h5>
                <p><?php echo $totalUsers; ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center">
                <h5>Total Patients</h5>
                <p><?php echo $totalPatients; ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center">
                <h5>Total Reports</h5>
                <p><?php echo $totalReports; ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center">
                <h5>Total Treatments</h5>
                <p><?php echo $totalTreatments; ?></p>
            </div>
        </div>
    </div>
    <a href="manage_users.php" class="btn btn-primary mt-4">Manage Users</a>
    <a href="manage_patients.php" class="btn btn-secondary mt-4">Manage Patients</a>
    <a href="manage_visits.php" class="btn btn-success mt-4">manage Visits</a>

</div>
</body>

</html>