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

<body style="background: #10465a; font-family: 'Arial', sans-serif; color: #fff;">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container p-4 bg-dark rounded">
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