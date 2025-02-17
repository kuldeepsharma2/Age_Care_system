<?php
session_start();
include './database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $full_name = $_POST['full_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $relation = $_POST['relation'];
    $age = $_POST['age'];
    
    // Patient's health data
    $medical_history = $_POST['medical_history'];
    $current_medications = $_POST['current_medications'];
    $allergies = $_POST['allergies'];
    $chronic_conditions = $_POST['chronic_conditions'];
    $blood_type = $_POST['blood_type'];

    // Validate required fields
    if (empty($full_name) || empty($dob) || empty($gender) || empty($email) || empty($password)|| empty($age)) {
        $_SESSION['error'] = "Please fill in all the required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
    } else {
        // Insert into Users Table
        $stmt = $pdo->prepare("INSERT INTO users (full_name, dob, gender, email, password, address, phone, relation, role, age) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$full_name, $dob, $gender, $email, $password, $address, $phone, $relation , 'user', $age])) {
            $user_id = $pdo->lastInsertId(); // Get newly inserted user ID

            // Insert into Patients Table with Health Data
            $stmt = $pdo->prepare("INSERT INTO patients (user_id, medical_history, current_medications, allergies, chronic_conditions, blood_type) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $medical_history, $current_medications, $allergies, $chronic_conditions, $blood_type]);

            $_SESSION['success'] = "Registration successful. Please login.";
            header("Location: ./login_user.php");
            exit();
        } else {
            $_SESSION['error'] = "Registration failed. Please try again.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">User Registration</h2>

    <?php if (isset($_SESSION['error'])) { echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>"; unset($_SESSION['error']); } ?>
    <?php if (isset($_SESSION['success'])) { echo "<div class='alert alert-success'>".$_SESSION['success']."</div>"; unset($_SESSION['success']); } ?>

    <form method="POST" class="d-flex flex-column justify-content-center align-items-center">
        <div class="form-group d-flex flex-column align-items-center w-50">
            <!-- User Info -->
            <input type="text" name="full_name" placeholder="Full Name" class="form-control mb-2" required>
            <input type="date" name="dob" class="form-control mb-2" required>
            <select name="gender" class="form-control mb-2" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
            <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
            <input type="text" name="address" placeholder="Address" class="form-control mb-2">
            <input type="text" name="phone" placeholder="Phone" class="form-control mb-2">
            <input type="text" name="age" placeholder="Age" class="form-control mb-2" required>
            <input type="text" name="relation" placeholder="Relation to Emergency Contact" class="form-control mb-2">

            <!-- Medical Info -->
            <textarea name="medical_history" placeholder="Medical History" class="form-control mb-2"></textarea>
            <textarea name="current_medications" placeholder="Current Medications" class="form-control mb-2"></textarea>
            <textarea name="allergies" placeholder="Allergies" class="form-control mb-2"></textarea>
            <textarea name="chronic_conditions" placeholder="Chronic Conditions" class="form-control mb-2"></textarea>
            <select name="blood_type" class="form-control mb-2" required>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
            </select>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-success w-100">Register</button>
            <a href="./login_user.php" class="btn btn-secondary mt-2 w-100">Already have an account? Login</a>
        </div>
    </form>
</div>
</body>

</html>