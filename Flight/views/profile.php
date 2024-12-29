<?php
session_start();


// Include database connection and passenger model
include '../php/includes/db.php';
include '../models/passengerModel.php';

// Fetch passenger details
$user_id = $_SESSION['id'];
$conn = connectToDB();
$stmt = $conn->prepare("SELECT * FROM passenger WHERE id = ?");
$stmt->execute([$user_id]);
$passenger = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission for updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $account_number = $_POST['account_number'];

    // Update passenger details
    $updateStmt = $conn->prepare("UPDATE passenger SET name = ?, email = ?, tel = ?, account_number = ? WHERE id = ?");
    $updateStmt->execute([$name, $email, $tel, $account_number, $user_id]);

    // Handle photo and passport image upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoPath = '../images/' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
        $photoPath=basename($_FILES['photo']['name']);
        $photoUpdateStmt = $conn->prepare("UPDATE passenger SET photo = ? WHERE id = ?");
        $photoUpdateStmt->execute([$photoPath, $user_id]);
    }

    if (isset($_FILES['passport_img']) && $_FILES['passport_img']['error'] === UPLOAD_ERR_OK) {
        $passportImgPath = '../images/' . basename($_FILES['passport_img']['name']);
        move_uploaded_file($_FILES['passport_img']['tmp_name'], $passportImgPath);
        $passportImgPath=basename($_FILES['passport_img']['name']);
        $passportImgUpdateStmt = $conn->prepare("UPDATE passenger SET passport_img = ? WHERE id = ?");
        $passportImgUpdateStmt->execute([$passportImgPath, $user_id]);
    }
    // Redirect to the same page to see the changes
    header("Location: profile.php");
    exit();

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #007bff;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #ffdd57;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        .profile-info {
            margin-bottom: 20px;
        }

        .profile-info p {
            font-size: 1.2rem;
            margin: 10px 0;
        }

        .profile-info strong {
            color: #007bff;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }
        .profile-photo {
            align-items: top;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
        }
        .profile-passport{

        }

        .form-group button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-group button:hover {
            background-color: #218838;
        }

        .form-group input[type="file"] {
            padding: 3px;
        }

        .form-group img {
            max-width: 100px;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<div class="navbar">
    <a href="passengerHome.php">Dashboard</a>
    <a href="#">Flights</a>
    <a href="?logout=true" class="logout-btn">Logout</a>
</div>

<div class="container">
    <h1>Profile</h1>
    <div class="profile-info">

        <img src="<?php echo '../images/' . $passenger['photo']; ?>" alt="Profile Image" class="profile-photo" >
        <img src="<?php echo '../images/'. $passenger['passport_img']; ?>" alt="Passport Image" class="profile-photo" >
        <p><strong>Name:</strong> <?php echo htmlspecialchars($passenger['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($passenger['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($passenger['tel']); ?></p>
        <p><strong>Account Number:</strong> <?php echo htmlspecialchars($passenger['account_number']); ?></p>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($passenger['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($passenger['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="tel">Phone</label>
            <input type="tel" id="tel" name="tel" value="<?php echo htmlspecialchars($passenger['tel']); ?>" required>
        </div>
        <div class="form-group">
            <label for="account_number">Account Number</label>
            <input type="text" id="account_number" name="account_number" value="<?php echo htmlspecialchars($passenger['account_number']); ?>" required>
        </div>
        <div class="form-group">
            <label for="photo">Profile Photo</label>
            <input type="file" id="photo" name="photo">
        </div>
        <div class="form-group">
            <label for="passport_img">Passport Image</label>
            <input type="file" id="passport_img" name="passport_img">
        </div>
        <div class="form-group">
            <button type="submit">Update Profile</button>
        </div>
    </form>
</div>
</body>
</html>