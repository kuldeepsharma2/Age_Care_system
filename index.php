<?php
session_start(); // Start session if using login system
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5 text-center">
    <h1>Welcome to Age Care System</h1>
    <p class="lead">A platform for managing healthcare, visit scheduling, and reports.</p>

    <div class="row mt-4">
        <!-- Staff Login Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-lg">
                <div class="card-body text-center">
                    <i class="bi bi-person-fill fs-3 mb-3 text-danger"></i> <!-- Bootstrap Icons -->
                    <h5 class="card-title">Staff Login</h5>
                    <p class="card-text">Access the staff portal to manage patient information and treatments.</p>
                    <a href="login.php" class="btn btn-danger btn-lg w-100">Login as Staff</a>
                </div>
            </div>
        </div>

        <!-- User Login Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-lg">
                <div class="card-body text-center">
                    <i class="bi bi-person-circle fs-3 mb-3 text-primary"></i> <!-- Bootstrap Icons -->
                    <h5 class="card-title">User Login</h5>
                    <p class="card-text">Login to view your health reports and communicate with caregivers.</p>
                    <a href="login_user.php" class="btn btn-primary btn-lg w-100">Login as User</a>
                </div>
            </div>
        </div>

        <!-- Admin Login Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-lg">
                <div class="card-body text-center">
                    <i class="bi bi-shield-lock fs-3 mb-3 text-danger"></i> <!-- Bootstrap Icons -->
                    <h5 class="card-title">Admin Login</h5>
                    <p class="card-text">Manage the entire system, including staff and user accounts.</p>
                    <a href="admin_login.php" class="btn btn-danger btn-lg w-100">Login as Admin</a>
                </div>
            </div>
        </div>

        <!-- Register as User Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-lg">
                <div class="card-body text-center">
                    <i class="bi bi-person-plus-fill fs-3 mb-3 text-success"></i> <!-- Bootstrap Icons -->
                    <h5 class="card-title">Register as User</h5>
                    <p class="card-text">Sign up to access the platform and manage your healthcare needs.</p>
                    <a href="register_user.php" class="btn btn-success btn-lg w-100">Register as User</a>
                </div>
            </div>
        </div>

        <!-- Register as Admin/Staff Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-lg">
                <div class="card-body text-center">
                    <i class="bi bi-person-badge fs-3 mb-3 text-success"></i> <!-- Bootstrap Icons -->
                    <h5 class="card-title">Register as Admin/Staff</h5>
                    <p class="card-text">Sign up as an Admin or Staff to manage users and patients.</p>
                    <a href="./register_staf_admin.php" class="btn btn-success btn-lg w-100">Register as Admin/Staff</a>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>

</body>

</html>