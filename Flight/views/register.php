<?php

session_start();
require_once '../models/UserModel2.php'; // Include the file where the register function resides

// Initialize error variables
$name_err = $email_err = $password_err = $tel_err = $account_err = $type_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect input data
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $tel = trim($_POST["tel"]);
    $account_number = trim($_POST["account_number"]);
    $type = trim($_POST["type"]);

    // Validate inputs
    if (empty($name))
        $name_err = "Please enter a name.";
    if (empty($email))
        $email_err = "Please enter an email.";
    if (empty($password))
        $password_err = "Please enter a password.";
    if (empty($tel))
        $tel_err = "Please enter a phone number.";
    if (empty($account_number))
        $account_err = "Please enter an account.";
    if (empty($type))
        $type_err = "Please select a user type.";

    // If no errors, proceed
    if (empty($name_err) && empty($email_err) && empty($password_err) && empty($tel_err) && empty($account_err) && empty($type_err)) {
        $_SESSION['user_data'] = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'tel' => $tel,
            'account_number' => $account_number,
            'type' => $type
        ];

        if ($type === 'passenger') {
            header("Location: register_passenger1.php");
            exit();
        } elseif ($type === 'company') {
            header("Location: register_company.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
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
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container">
                    <h2 class="text-center mb-4">Register</h2>
                    <form method="POST" action="">
                        <div class="form-group mb-3">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                            <small class="text-danger"><?php echo $name_err; ?></small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                            <small class="text-danger"><?php echo $email_err; ?></small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <small class="text-danger"><?php echo $password_err; ?></small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="tel">Phone</label>
                            <input type="tel" name="tel" id="tel" class="form-control" required>
                            <small class="text-danger"><?php echo $tel_err; ?></small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="account_number">Account Number</label>
                            <input type="text" name="account_number" id="account_number" class="form-control" required>
                            <small class="text-danger"><?php echo $account_err; ?></small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="">Select</option>
                                <option value="passenger">Passenger</option>
                                <option value="company">Company</option>
                            </select>
                            <small class="text-danger"><?php echo $type_err; ?></small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>

                    <hr class="my-4">
                    <div class="text-center">
                        <p>Already have an account? <a href="login.php" class="btn btn-link">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>