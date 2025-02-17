<?php
session_start();
include '../database.php';

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all users except admin
$stmt = $pdo->query("SELECT * FROM users WHERE role != 'admin'");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle user update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $userId = $_POST['user_id'];
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $relation = $_POST['relation'] ?? null;

    // Update SQL query without the excluded fields
    $stmt = $pdo->prepare("UPDATE users SET 
        full_name = ?, email = ?, role = ?, dob = ?, gender = ?, address = ?, phone = ?, 
        emergency_contact_name = ?, emergency_contact_phone = ?, relation = ? 
        WHERE id = ?");
    
    $stmt->execute([$fullName, $email, $role, $dob, $gender, $address, $phone, 
         $relation, $userId]);

    $_SESSION['success'] = "User updated successfully!";
    header("Location: manage_users.php");
    exit();
}

// Handle user delete
if (isset($_GET['delete'])) {
    $userId = $_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
    $_SESSION['success'] = "User deleted successfully!";
    header("Location: manage_users.php");
    exit();
}
?>

<?php include '../includes/header_admin.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">Manage Users</h2>

    <!-- Success Message -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; ?></div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Users Table -->
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo ucfirst($user['role']); ?></td>
                <td><?php echo htmlspecialchars($user['phone'] ?? ''); ?></td>
                <td>
                    <!-- Edit Button with data attributes -->
                    <button class="btn btn-warning edit-btn" data-bs-toggle="modal" data-bs-target="#editUserModal"
                        data-id="<?php echo $user['id']; ?>"
                        data-name="<?php echo htmlspecialchars($user['full_name'], ENT_QUOTES); ?>"
                        data-email="<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>"
                        data-role="<?php echo $user['role']; ?>" data-dob="<?php echo $user['dob'] ?? ''; ?>"
                        data-gender="<?php echo $user['gender'] ?? ''; ?>"
                        data-address="<?php echo $user['address'] ?? ''; ?>"
                        data-phone="<?php echo $user['phone'] ?? ''; ?>"
                        data-relation="<?php echo $user['relation'] ?? ''; ?>">
                        Edit
                    </button>
                    <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-danger"
                        onclick="return confirm('Delete this user?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="user_id" id="modalUserId">
                    <div class="mb-3"><label>Full Name</label><input type="text" name="full_name" id="modalFullName"
                            class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" id="modalEmail"
                            class="form-control" required></div>
                    <div class="mb-3"><label>Role</label><select name="role" id="modalRole" class="form-control">
                            <option value="staff">Staff</option>
                            <option value="user">User</option>
                            <option value="family">Family</option>
                        </select></div>
                    <div class="mb-3"><label>Phone</label><input type="text" name="phone" id="modalPhone"
                            class="form-control"></div>
                    <button type="submit" name="update_user" class="btn btn-primary w-100">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Fill Modal Fields -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function() {
            document.getElementById("modalUserId").value = this.getAttribute("data-id");
            document.getElementById("modalFullName").value = this.getAttribute("data-name");
            document.getElementById("modalEmail").value = this.getAttribute("data-email");
            document.getElementById("modalRole").value = this.getAttribute("data-role");
            document.getElementById("modalPhone").value = this.getAttribute("data-phone");
        });
    });
});
</script>

</body>

</html>