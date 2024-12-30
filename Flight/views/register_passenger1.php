<?php
session_start();
require_once '../models/UserModel2.php';
require_once '../models/PassengerModel.php'; //Include the file where the register function resides

if (!isset($_SESSION['user_data'])) {
    header("Location: register.php");
    exit();
}

// Initialize variables and errors
$photo = $passportImg = "";
$photo_err = $passportImg_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_FILES["photo"]["name"])) {
        $photo_err = "Please upload a photo.";
    } else {
        $photo = $_FILES["photo"]["name"];
        move_uploaded_file($_FILES["photo"]["tmp_name"], "../images/" . $photo);
    }

    if (empty($_FILES["passport_img"]["name"])) {
        $passportImg_err = "Please upload a passport image.";
    } else {
        $passportImg = $_FILES["passport_img"]["name"];
        move_uploaded_file($_FILES["passport_img"]["tmp_name"], "../images/" . $passportImg);
    }

    if (empty($photo_err) && empty($passportImg_err)) {
        // Retrieve session data
        $userData = $_SESSION['user_data'];
        $name = $userData['name'];
        $email = $userData['email'];
        $password = $userData['password'];
        $tel = $userData['tel'];
        $account_number = $userData['account_number'];
      

        // Call the register function
        $registerSuccess = registerPassenger($name, $email, $password, $tel, $account_number ,$photo, $passportImg);

        if ($registerSuccess) {

            header("Location: login.php"); // Redirect to login page
            exit();
        } else {
            echo "<div class='alert alert-danger text-center'>Registration failed. Email or name already exists.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Registration</title>
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
                    <h2 class="text-center mb-4">Passenger Registration</h2>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                            <label for="photo">Photo</label>
                            <input type="file" name="photo" id="photo" class="form-control" required>
                            <?php if (!empty($photo_err)) : ?>
                                <small class="text-danger"><?php echo $photo_err; ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group mb-3">
                            <label for="passport_img">Passport Image</label>
                            <input type="file" name="passport_img" id="passport_img" class="form-control" required>
                            <?php if (!empty($passportImg_err)) : ?>
                                <small class="text-danger"><?php echo $passportImg_err; ?></small>
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
