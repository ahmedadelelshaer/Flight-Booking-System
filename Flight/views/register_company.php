<?php
session_start();
require_once '../models/UserModel2.php'; // Include the file where the register function resides

if (!isset($_SESSION['user_data'])) {
    header("Location: register.php");
    exit();
}

// Initialize variables and errors
$logoImg = $bio = $address = "";
$logoImg_err = $bio_err = $address_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate logo image
    if (empty($_FILES["logo_img"]["name"])) {
        $logoImg_err = "Please upload a company logo.";
    } else {
        $logoImg = $_FILES["logo_img"]["name"];
        move_uploaded_file($_FILES["logo_img"]["tmp_name"], "../images/" . $logoImg); // Move to the desired folder
    }

    // Validate bio
    if (empty(trim($_POST["bio"]))) {
        $bio_err = "Please provide a company bio.";
    } else {
        $bio = trim($_POST["bio"]);
    }

    // Validate address
    if (empty(trim($_POST["address"]))) {
        $address_err = "Please provide a company address.";
    } else {
        $address = trim($_POST["address"]);
    }

    if (empty($logoImg_err) && empty($bio_err) && empty($address_err)) {
        // Retrieve session data
        $userData = $_SESSION['user_data'];
        $name = $userData['name'];
        $email = $userData['email'];
        $password = $userData['password'];
        $tel = $userData['tel'];
        $account_number = $userData['account_number'];
        $type = $userData['type'];

        // Call the register function, passing the logo image as logo_img
        $registerSuccess = registerCompany($name, $email, $password, $tel, $account_number, $bio, $address, $logoImg);
        if ($registerSuccess) {
            session_destroy(); // Clear session
            header("Location: login.php"); // Redirect to login page
            exit();
        } else {
            echo "<div class='alert alert-danger text-center'>Registration failed. Email or name already exists.</div>";
        }
    }
}?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #10465a;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        .form-container {
            background-color: #1d6a85;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .btn-primary {
            background-color: #1d3557;
            border-color: #1d3557;
        }

        .btn-primary:hover {
            background-color: #457b9d;
            border-color: #457b9d;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container">
                    <h2 class="text-center mb-4">Company Registration</h2>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                            <label for="logo_img">Company Logo</label>
                            <input type="file" name="logo_img" id="logo_img" class="form-control" required>
                            <?php if (!empty($logoImg_err)): ?>
                                <small class="text-danger"><?php echo $logoImg_err; ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group mb-3">
                            <label for="bio">Company Bio</label>
                            <textarea name="bio" id="bio" class="form-control" rows="4" required></textarea>
                            <?php if (!empty($bio_err)): ?>
                                <small class="text-danger"><?php echo $bio_err; ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group mb-3">
                            <label for="address">Company Address</label>
                            <textarea name="address" id="address" class="form-control" rows="4" required></textarea>
                            <?php if (!empty($address_err)): ?>
                                <small class="text-danger"><?php echo $address_err; ?></small>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Complete Registration</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>