<?php
session_start();
include '../database.php';

// Restrict access to staff only
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../login.php");
    exit();
}

// Fetch staff's assigned patients
$staffId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT p.id AS patient_id, u.full_name 
                       FROM patients p
                       JOIN users u ON p.user_id = u.id
                       WHERE p.assigned_staff = ?");
$stmt->execute([$staffId]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all reports for patients assigned to the staff
$reports = [];
if (!empty($patients)) {
    $patientIds = array_column($patients, 'patient_id');
    $placeholders = implode(',', array_fill(0, count($patientIds), '?'));
    $stmt = $pdo->prepare("SELECT r.id, r.report_type, r.findings, r.created_at, p.user_id 
                           FROM reports r
                           JOIN patients p ON r.patient_id = p.id
                           WHERE p.id IN ($placeholders)");
    $stmt->execute($patientIds);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include '../includes/header_user.php'; ?>

<!-- Edit Report Modal -->
<div class="modal fade" id="editReportModal" tabindex="-1" aria-labelledby="editReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editReportModalLabel">Edit Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editReportForm">
                    <input type="hidden" id="reportId" name="report_id">
                    <div class="mb-3">
                        <label for="reportType" class="form-label">Report Type</label>
                        <input type="text" class="form-control" id="reportType" name="report_type" required>
                    </div>
                    <div class="mb-3">
                        <label for="findings" class="form-label">Findings</label>
                        <textarea class="form-control" id="findings" name="findings" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <h2 class="text-center">View and Edit Reports</h2>

    <?php if (count($reports) > 0): ?>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Report Type</th>
                <th>Findings</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
            <tr>
                <td><?php echo $report['report_type']; ?></td>
                <td><?php echo $report['findings']; ?></td>
                <td><?php echo $report['created_at']; ?></td>
                <td>
                    <!-- Edit button triggers the modal and fetches report data -->
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editReportModal"
                        onclick="loadReportData(<?php echo $report['id']; ?>)">
                        Edit
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No reports available for your assigned patients.</p>
    <?php endif; ?>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Function to load report data into the modal
function loadReportData(reportId) {
    $.ajax({
        url: 'get_report.php', // Fetch report data
        method: 'GET',
        data: {
            report_id: reportId
        },
        success: function(response) {
            var report = JSON.parse(response);
            if (report.error) {
                alert(report.error); // Display error message if report not found
            } else {
                $('#reportId').val(report.id);
                $('#reportType').val(report.report_type);
                $('#findings').val(report.findings);
            }
        },
        error: function() {
            alert('Error loading report data.');
        }
    });
}

// Form submission to update the report
$('#editReportForm').on('submit', function(event) {
    event.preventDefault();

    $.ajax({
        url: 'update_report.php', // File to handle report update
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            alert(response); // Success message
            $('#editReportModal').modal('hide');
            location.reload(); // Reload the page to reflect changes
        },
        error: function() {
            alert('Failed to update report.');
        }
    });
});
</script>

</body>

</html>