<?php
session_start(); // Start session if using login system
?>
<?php

include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Store user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];

        // Debugging: Check role before redirect
        error_log("User Role: " . $_SESSION['role']);

        // Redirect based on role
        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
            exit();
        } elseif ($user['role'] == 'staff') {
            header("Location: staff/dashboard.php");
            exit();
        } elseif ($user['role'] == 'user') {
            header("Location: user/dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid user role!";
        }
    } else {
        $_SESSION['error'] = "Invalid credentials!";
    }
}

// If session error exists, display it
if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']); // Clear error after displaying
}
?>

<?php include 'includes/header.php'; ?>
<div class="container mt-5">
    <h2>Login</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>