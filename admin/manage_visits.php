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

// Possible status options
$statusOptions = ['Pending', 'Approved', 'Completed', 'Canceled'];

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
    $visitDate = $_POST['visit_date'];

    $stmt = $pdo->prepare("UPDATE visits SET visit_date = ? WHERE id = ?");
    if ($stmt->execute([$visitDate, $visitId])) {
        $_SESSION['success'] = "Visit updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update visit.";
    }
    header("Location: manage_visits.php");
    exit();
}

// Handle status update via AJAX
if (isset($_POST['status_update'])) {
    $visitId = $_POST['visit_id'];
    $newStatus = $_POST['status'];

    if (!empty($newStatus) && in_array($newStatus, $statusOptions)) {
        $stmt = $pdo->prepare("UPDATE visits SET status = ? WHERE id = ?");
        if ($stmt->execute([$newStatus, $visitId])) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "invalid"; // Prevent invalid status update
    }
    exit();
}
?>

<?php include '../includes/header_admin.php'; ?>
<div class="container mt-5">
    <h2>Manage Visits</h2>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <table class="table table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Staff Member</th>
                <th>Visit Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($visits as $visit): ?>
            <tr>
                <td><?php echo $visit['user_name']; ?></td>
                <td><?php echo $visit['user_email']; ?></td>
                <td><?php echo $visit['staff_name']; ?></td>
                <td>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="visit_id" value="<?php echo $visit['id']; ?>">
                        <input type="datetime-local" name="visit_date" class="form-control visitDate"
                            value="<?php echo date('Y-m-d\TH:i', strtotime($visit['visit_date'])); ?>" required>
                        <button type="submit" name="update" class="btn btn-sm btn-primary mt-2">Update</button>
                    </form>
                </td>
                <td>
                    <select class="form-control statusSelect" data-id="<?php echo $visit['id']; ?>">
                        <?php foreach ($statusOptions as $status): ?>
                        <option value="<?php echo $status; ?>"
                            <?php echo ($visit['status'] == $status) ? 'selected' : ''; ?>>
                            <?php echo $status; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-sm btn-success mt-2 updateStatusBtn"
                        data-id="<?php echo $visit['id']; ?>">Update Status</button>
                </td>
                <td>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="visit_id" value="<?php echo $visit['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- JavaScript for Status Update -->
<script>
$(document).ready(function() {
    $(".updateStatusBtn").on("click", function() {
        let visitId = $(this).data("id");
        let newStatus = $(".statusSelect[data-id='" + visitId + "']").val();

        $.ajax({
            url: "manage_visits.php",
            type: "POST",
            data: {
                visit_id: visitId,
                status: newStatus,
                status_update: true
            },
            success: function(response) {
                if (response === "success") {
                    alert("Status updated successfully!");
                } else if (response === "invalid") {
                    alert("Invalid status update.");
                } else {
                    alert("Failed to update status.");
                }
            },
            error: function() {
                alert("Failed to update status.");
            }
        });
    });

    let today = new Date();
    let minDateTime = today.toISOString().slice(0, 16);

    // Restrict past date selection
    document.querySelectorAll(".visitDate").forEach(function(input) {
        input.setAttribute("min", minDateTime);
    });
});
</script>

</body>

</html>