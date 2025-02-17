<?php
session_start(); // Start session if using login system
?>
<?php

include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'user'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'user';
        header("Location: user/dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid credentials.";
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">User Login</h2>
    <?php if (isset($_SESSION['error'])) { echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>"; unset($_SESSION['error']); } ?>
    <form method="POST" class="d-flex flex-column justify-content-center align-items-center ">
        <div class="form-group d-flex flex-column align-items-center w-50">
            <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
            <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <a href="register_user.php" class="btn btn-secondary mt-2 w-100">Register</a>
        </div>
    </form>





</div>
</div>
</body>

</html>