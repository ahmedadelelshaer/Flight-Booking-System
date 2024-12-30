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