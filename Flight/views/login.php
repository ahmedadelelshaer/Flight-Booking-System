<?php
$pageTitle = "Login";
include_once '../php/includes/db.php';

session_start();
include_once '../models/UserModel2.php';

// If the user is already logged in, redirect to their homepage
if (isset($_SESSION['id'])) {
    // Redirect based on user type
    if ($_SESSION['type'] == 'company') {
        header("Location: company_home.php");
        exit();
    } elseif ($_SESSION['type'] == 'passenger') {
        header("Location: passengerHome.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data_check = login($_POST['email'], $_POST['password']);
    if (is_array($data_check)) {
        // Save user data in the session
        $_SESSION['id'] = $data_check['id'];
        $_SESSION['email'] = $data_check['email'];
        $_SESSION['name'] = $data_check['name'];

        // Detect user type
        if (!empty($data_check['photo'])) {
            $_SESSION['type'] = 'passenger'; // It's a passenger
        } elseif (!empty($data_check['bio'])) {
            $_SESSION['type'] = 'company'; // It's a company
            $_SESSION['company_id'] = $data_check['id']; // Store company ID
        } else {
            $_SESSION['type'] = 'unknown'; // Handle unexpected cases
        }

        // Redirect based on user type after successful login
        if ($_SESSION['type'] == 'company') {
            header('Location: company_home.php');
        } elseif ($_SESSION['type'] == 'passenger') {
            header('Location: passengerHome.php');
        } else {
            // Redirect to a default page for unknown user types
            header('Location: login.php');
        }
        exit();
    } else {
        $error_message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background: #10465a; font-family: 'Arial', sans-serif; color: #fff;">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="form-container">
                <h2 class="text-center mb-4">Login</h2>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
                <p class="mt-3">Don't have an account? <a href="register.php">Register here</a></p>
                <p class="mt-3">Already logged in? <a href="logout.php">Log out</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Add Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
