<?php
session_start();
include '../database.php';

// Restrict access to users only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch staff members for selection
$stmt = $pdo->query("SELECT id, full_name FROM users WHERE role = 'staff'");
$staffMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle visit scheduling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staffId = $_POST['staff_id'];
    $visitDate = $_POST['visit_date'];

    $stmt = $pdo->prepare("INSERT INTO visits (user_id, staff_id, visit_date) VALUES (?, ?, ?)");
    if ($stmt->execute([$userId, $staffId, $visitDate])) {
        $_SESSION['success'] = "Visit scheduled successfully!";
        header("Location: schedule_visit.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to schedule visit.";
    }
}

// Fetch scheduled visits for the logged-in user
$stmt = $pdo->prepare("SELECT v.visit_date, u.full_name AS staff_name, v.status 
                       FROM visits v
                       JOIN users u ON v.staff_id = u.id
                       WHERE v.user_id = ? ORDER BY v.visit_date ASC");
$stmt->execute([$userId]);
$scheduledVisits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header_user.php'; ?>
<div class="container mt-5">
    <h2>Schedule a Visit</h2>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; ?></div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Select Staff</label>
            <select name="staff_id" class="form-control" required>
                <?php foreach ($staffMembers as $staff): ?>
                <option value="<?php echo $staff['id']; ?>"><?php echo $staff['full_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Visit Date</label>
            <input type="datetime-local" name="visit_date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Schedule Visit</button>
    </form>

    <hr>

    <h3>Scheduled Visits</h3>
    <?php if (count($scheduledVisits) > 0): ?>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Visit Date</th>
                <th>Staff Member</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scheduledVisits as $visit): ?>
            <tr>
                <td><?php echo date("F j, Y, g:i a", strtotime($visit['visit_date'])); ?></td>
                <td><?php echo $visit['staff_name']; ?></td>
                <td><?php echo ucfirst($visit['status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="alert alert-warning mt-3">You have no scheduled visits.</div>
    <?php endif; ?>

</div>
</body>

</html>