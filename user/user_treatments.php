<?php
session_start();
include '../database.php';

// Restrict access to logged-in users only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch treatments for the logged-in user
$stmt = $pdo->prepare("SELECT t.id, t.disease, t.medicine, t.treatment_details, t.created_at, s.full_name AS prescribed_by 
                       FROM treatments t
                       JOIN patients p ON t.patient_id = p.id
                       JOIN users s ON t.prescribed_by = s.id
                       WHERE p.user_id = ?");
$stmt->execute([$userId]);
$treatments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header_user.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">My Treatment Records</h2>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; ?></div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (count($treatments) > 0): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Disease</th>
                <th>Medicine</th>
                <th>Treatment Details</th>
                <th>Prescribed By</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($treatments as $treatment): ?>
            <tr>
                <td><?php echo $treatment['disease']; ?></td>
                <td><?php echo $treatment['medicine']; ?></td>
                <td><?php echo $treatment['treatment_details']; ?></td>
                <td><?php echo $treatment['prescribed_by']; ?></td>
                <td><?php echo date("F j, Y, g:i a", strtotime($treatment['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="alert alert-warning">No treatments found for your profile.</div>
    <?php endif; ?>


</div>
</body>

</html>