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

        // Detect user type and store company ID if it's a company
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
    <title>Login - AirBook</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            color: #333;
            margin: 0;
            padding: 0;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed; /* Make the background stay fixed */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../images/sky.jpg') no-repeat center center/cover; /* Ensure the image covers the viewport */
            background-attachment: fixed; /* Keep the image fixed while scrolling */
            opacity: 30%; /* Adjust the opacity as needed */
            z-index: -1; /* Ensure the image is behind the content */
        }
        .form-container {
            background-color: rgba(255, 255, 255, 0.1); /* Transparent background */
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .form-container h2 {
            font-size: 30px;
            color: #fff;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group label {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
        }

        .form-control {
            background-color: #f1f1f1;
            border-radius: 5px;
            padding: 15px;
            border: none;
            color: #333;
        }

        .form-control:focus {
            background-color: #ffffff;
            box-shadow: 0 0 8px rgba(0, 102, 204, 0.5);
            border-color: #007bff;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 5px;
            padding: 10px;
        }

        .icon {
            position: absolute;
            color: black;
            margin-top: 46px;
            margin-left: 520px;
        }
        .form-container .footer {
            text-align: center;
            margin-top: 20px;
        }

        .footer a {
            color: #fff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .container {
            margin-top: 100px;
        }

    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="form-container">
                <h2>Login to AirBook</h2>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group mb-3 position-relative">
                        <label for="email"><i class="fas fa-envelope icon"></i> Email</label>
                        <input type="email" name="email" id="email" class="form-control" required placeholder="Enter your email">
                    </div>
                    <div class="form-group mb-3 position-relative">
                        <label for="password"><i class="fas fa-lock icon"></i> Password</label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Enter your password">
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
                <p class="footer mt-3">Don't have an account? <a href="register_passenger1.php">Register here</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Add Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
