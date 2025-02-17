<?php
session_start(); // Start session if using login system
?>
<?php

include './database.php';

$admin_code = "805397"; // Secure code for Admin & Staff registration

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $entered_code = $_POST['access_code'];
    $role = $_POST['role']; // User selects 'admin' or 'staff'

    if ($entered_code !== $admin_code) {
        $_SESSION['error'] = "Invalid Access Code.";
    } else {
        // Insert user into the database
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$full_name, $email, $password, $role])) {
            $_SESSION['success'] = ucfirst($role) . " registered successfully. Please login.";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Registration failed.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">Register as Admin or Staff</h2>
    <?php if (isset($_SESSION['error'])) { echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>"; unset($_SESSION['error']); } ?>
    <form method="POST" class="d-flex flex-column justify-content-center align-items-center ">
        <div class="form-group d-flex flex-column align-items-center w-50">
            <input type="text" name="full_name" placeholder="Full Name" class="form-control mb-2" required>
            <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
            <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
            <input type="text" name="access_code" placeholder="Access Code" class="form-control mb-2" required>
            <select name="role" class="form-control mb-2" required>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
            </select>
            <button type="submit" class="btn btn-danger w-100">Register</button>
            <a href="index.php" class="btn btn-secondary mt-2 w-100">Already registered? Login</a>
        </div>
    </form>


</div>
</body>

</html>