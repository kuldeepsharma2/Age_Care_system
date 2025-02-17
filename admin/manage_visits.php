<?php
session_start();
include '../database.php';

// Restrict access to admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all visits
$stmt = $pdo->query("SELECT v.*, u.full_name AS user_name, u.email AS user_email, s.full_name AS staff_name FROM visits v JOIN users u ON v.user_id = u.id JOIN users s ON v.staff_id = s.id");
$visits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle visit deletion
if (isset($_POST['delete'])) {
    $visitId = $_POST['visit_id'];
    $stmt = $pdo->prepare("DELETE FROM visits WHERE id = ?");
    if ($stmt->execute([$visitId])) {
        $_SESSION['success'] = "Visit deleted!";
    } else {
        $_SESSION['error'] = "Failed to delete visit.";
    }
    header("Location: manage_visits.php");
    exit();
}

// Handle visit update
if (isset($_POST['update'])) {
    $visitId = $_POST['visit_id'];
    $staffId = $_POST['staff_id'];
    $visitDate = $_POST['visit_date'];
    
    $stmt = $pdo->prepare("UPDATE visits SET staff_id = ?, visit_date = ? WHERE id = ?");
    if ($stmt->execute([$staffId, $visitDate, $visitId])) {
        $_SESSION['success'] = "Visit updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update visit.";
    }
    header("Location: manage_visits.php");
    exit();
}
?>

<?php include '../includes/header_admin.php'; ?>
<div class="container mt-5">
    <h2>Manage Visits</h2>
    <ul class="list-group">
        <?php foreach ($visits as $visit): ?>
        <li class="list-group-item">
            <strong>User:</strong> <?php echo $visit['user_name']; ?> - <strong>Email:</strong>
            <?php echo $visit['user_email']; ?> - <strong>Staff:</strong>
            <?php echo $visit['staff_name']; ?> - <strong>Date:</strong> <?php echo $visit['visit_date']; ?>

            <!-- Button to trigger update modal -->
            <button type="button" class="btn btn-warning btn-sm mt-2" data-toggle="modal"
                data-target="#updateVisitModal<?php echo $visit['id']; ?>">Update</button>

            <!-- Modal for updating visit -->
            <div class="modal fade" id="updateVisitModal<?php echo $visit['id']; ?>" tabindex="-1"
                aria-labelledby="updateVisitModalLabel<?php echo $visit['id']; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateVisitModalLabel<?php echo $visit['id']; ?>">Update Visit
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <input type="hidden" name="visit_id" value="<?php echo $visit['id']; ?>">

                                <!-- Select Staff for update -->
                                <div class="mb-3">
                                    <label>Select Staff</label>
                                    <select name="staff_id" class="form-control" required>
                                        <?php 
                                        $staffStmt = $pdo->query("SELECT id, full_name FROM users WHERE role = 'staff'");
                                        $staffMembers = $staffStmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($staffMembers as $staff): 
                                        ?>
                                        <option value="<?php echo $staff['id']; ?>"
                                            <?php echo $staff['id'] == $visit['staff_id'] ? 'selected' : ''; ?>>
                                            <?php echo $staff['full_name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Visit Date for update -->
                                <div class="mb-3">
                                    <label>Visit Date</label>
                                    <input type="datetime-local" name="visit_date" class="form-control"
                                        value="<?php echo date('Y-m-d\TH:i', strtotime($visit['visit_date'])); ?>"
                                        required>
                                </div>

                                <button type="submit" name="update" class="btn btn-success w-100">Update Visit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>