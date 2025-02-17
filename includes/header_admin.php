<?php


// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Age Care System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

</head>

<body class="light-mode">
    <nav class="navbar navbar-expand-lg navbar-light bg-purple">
        <div class="container">
            <!-- Logo with no navigation -->
            <span class="navbar-brand text-white" style="cursor: default;">Age Care System</span>

            <div>
                <!-- <button class="btn btn-outline-light" id="dark-mode-toggle">🌙 Dark Mode</button> -->
                <!-- WhatsApp Icon -->
                <a href="https://wa.me/61481297874?text=Hi%20I%20have%20a%20Query" target="_blank"
                    class="btn btn-success">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="btn btn-danger">Logout</a>
                <?php else: ?>
                <a href="admin_login.php" class="btn btn-primary">Admin Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb Navigation -->
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <?php if ($current_page !== 'index.php'): ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo ucfirst(str_replace('.php', '', $current_page)); ?>
                </li>
                <?php endif; ?>
            </ol>


        </nav>

        <!-- Back Button (Not on Homepage) -->
        <?php if ($current_page !== 'index.php'): ?>
        <button class="btn btn-secondary" onclick="history.back()">Back</button>
        <?php endif; ?>
    </div>
</body>

</html>