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
