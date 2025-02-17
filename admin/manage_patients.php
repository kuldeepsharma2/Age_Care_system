<?php
session_start();
include '../database.php';

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch patients with assigned staff and their medical history from the patients table
$stmt = $pdo->query("SELECT p.*, usr.full_name AS patient_name, usr.email AS patient_email, 
                          u.full_name AS staff_name
                    FROM patients p 
                    JOIN users usr ON p.user_id = usr.id
                    LEFT JOIN users u ON p.assigned_staff = u.id");
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch only users with the 'user' role
$stmt = $pdo->query("SELECT id, full_name FROM users WHERE role = 'user'");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch staff members
$stmt = $pdo->query("SELECT id, full_name FROM users WHERE role = 'staff'");
$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle adding a new patient
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    // Ensure 'user_id' is set and not empty
    if (!empty($_POST['user_id'])) {
        $userId = $_POST['user_id'];
        $staffId = $_POST['staff_id'] ?? null;

        // Collect medical history and other health-related data from the form input
        $medicalHistory = $_POST['medical_history'] ?? 'No history available';
        $bloodType = $_POST['blood_type'] ?? 'Unknown';
        $allergies = $_POST['allergies'] ?? 'No allergies';
        $chronicConditions = $_POST['chronic_conditions'] ?? 'No chronic conditions';
        $currentMedications = $_POST['current_medications'] ?? 'No medications';

        // Insert new patient with all data into the patients table
        $stmt = $pdo->prepare("INSERT INTO patients (user_id, assigned_staff, medical_history, blood_type, allergies, chronic_conditions, current_medications) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$userId, $staffId, $medicalHistory, $bloodType, $allergies, $chronicConditions, $currentMedications]);
            $_SESSION['success'] = "Patient added successfully!";
            header("Location: manage_patients.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "User ID is required to add a patient.";
    }
}

// Handle editing an existing patient
if (isset($_GET['edit'])) {
    $patientId = $_GET['edit'];
    // Fetch the existing patient data for editing
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$patientId]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch staff members for assigning/editing staff
    $stmt = $pdo->query("SELECT id, full_name FROM users WHERE role = 'staff'");
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle assigning staff to patient
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['staff_id']) && isset($_POST['patient_id'])) {
    $staffId = $_POST['staff_id'];
    $patientId = $_POST['patient_id'];

    // Update the assigned staff
    $stmt = $pdo->prepare("UPDATE patients SET assigned_staff = ? WHERE id = ?");
    try {
        $stmt->execute([$staffId, $patientId]);
        $_SESSION['success'] = "Staff assigned successfully!";
        header("Location: manage_patients.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

// Delete patient (with security)
if (isset($_GET['delete'])) {
    $patientId = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->execute([$patientId]);
    $_SESSION['success'] = "Patient deleted successfully!";
    header("Location: manage_patients.php");
    exit();
}
?>

<?php include '../includes/header_admin.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">Manage Patients</h2>

    <!-- Success Message -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; ?></div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Form to Add or Edit Patient -->
    <h4 class="mt-4">Add/Edit Patient</h4>
    <form method="POST">
        <div class="mb-3">
            <label>Select User</label>
            <select name="user_id" class="form-control" required>
                <option value="">-- Select a User --</option>
                <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>"
                    <?php echo isset($patient) && $patient['user_id'] == $user['id'] ? 'selected' : ''; ?>>
                    <?php echo $user['full_name']; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Patient's Medical Information -->
        <div class="mb-3">
            <label>Medical History</label>
            <textarea name="medical_history" class="form-control"
                rows="3"><?php echo isset($patient) ? $patient['medical_history'] : ''; ?></textarea>
        </div>
        <div class="mb-3">
            <label>Blood Type</label>
            <input type="text" name="blood_type" class="form-control"
                value="<?php echo isset($patient) ? $patient['blood_type'] : ''; ?>">
        </div>
        <div class="mb-3">
            <label>Allergies</label>
            <textarea name="allergies" class="form-control"
                rows="2"><?php echo isset($patient) ? $patient['allergies'] : ''; ?></textarea>
        </div>
        <div class="mb-3">
            <label>Chronic Conditions</label>
            <textarea name="chronic_conditions" class="form-control"
                rows="2"><?php echo isset($patient) ? $patient['chronic_conditions'] : ''; ?></textarea>
        </div>
        <div class="mb-3">
            <label>Current Medications</label>
            <textarea name="current_medications" class="form-control"
                rows="2"><?php echo isset($patient) ? $patient['current_medications'] : ''; ?></textarea>
        </div>

        <div class="mb-3">
            <label>Assign to Staff</label>
            <select name="staff_id" class="form-control">
                <option value="">-- Optional: Assign a Staff --</option>
                <?php foreach ($staff as $s): ?>
                <option value="<?php echo $s['id']; ?>"
                    <?php echo isset($patient) && $patient['assigned_staff'] == $s['id'] ? 'selected' : ''; ?>>
                    <?php echo $s['full_name']; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <?php echo isset($patient) ? 'Update Patient' : 'Add Patient'; ?>
        </button>
    </form>

    <!-- Patients List -->
    <h4 class="mt-4">Existing Patients</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Medical History</th>
                <th>Assigned Staff</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($patients as $patient): ?>
            <tr>
                <td><?php echo $patient['patient_name']; ?></td>
                <td><?php echo $patient['patient_email']; ?></td>
                <td><?php echo $patient['medical_history'] ?: 'No History'; ?></td>
                <td><?php echo $patient['staff_name'] ?: 'Not Assigned'; ?></td>
                <td style="text-align:center;">
                    <a href="?edit=<?php echo $patient['id']; ?>" class="btn btn-info">Edit</a>
                    <a href="?delete=<?php echo $patient['id']; ?>" class="btn btn-danger"
                        onclick="return confirm('Delete this patient?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>

</html>