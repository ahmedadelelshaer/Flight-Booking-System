<?php
session_start();
include_once '../php/includes/db.php';
$conn = connectToDB();

// Assuming your DB class has the updateCompanyProfile method
include_once '../models/CompanyModel.php';
$companyModel = new CompanyModel($conn);

// Check if the user is logged in and is a company admin
if (!isset($_SESSION['id']) || $_SESSION['type'] != 'company') {
    header("Location: login.php");
    exit();
}

// Fetch company data
$companyId = $_SESSION['id'];
$stmt = $conn->prepare("SELECT name, bio, address, logo_img FROM company WHERE id = ?");
$stmt->execute([$companyId]);
$company = $stmt->fetch();

// Fetch flights for the company
$stmt = $conn->prepare("SELECT id, name, source, destination, start_datetime, end_datetime FROM flights WHERE company_id = ?");
$stmt->execute([$companyId]);
$flights = $stmt->fetchAll();

// Logout logic
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Update company info logic
if (isset($_FILES['logo_img']) && $_FILES['logo_img']['error'] == 0) {
    // Get the original filename
    $logoPath = basename($_FILES['logo_img']['name']);

    // Optionally sanitize the filename to prevent any issues
    $logoPath = preg_replace("/[^a-zA-Z0-9\-_\.]/", "", $logoPath); // Allow only safe characters

    $targetDirectory = "../images/"; // Assuming the images are stored in this folder

    // Ensure the images directory exists and is writable
    if (move_uploaded_file($_FILES['logo_img']['tmp_name'], $targetDirectory . $logoPath)) {
        // Image uploaded successfully, save the filename in the database
    } else {
        $error = "Failed to upload logo image.";
    }
} else {
    $logoPath = $company['logo_img']; // Keep the current logo if no new one is uploaded
}

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $address = $_POST['address'];

    // Save only the filename in the database, not the full path
    $companyModel->updateCompanyProfile($companyId, $name, $bio, $address, $logoPath);

    // Redirect to the same page to refresh
    header("Location: company_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Add a separate CSS file -->
</head>
<style>
    /* Include any custom styles you need here */
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
        background: url('../images/pexels-ahmedmuntasir-912050.jpg') no-repeat center center/cover; /* Ensure the image covers the viewport */
        background-attachment: fixed; /* Keep the image fixed while scrolling */
        opacity: 25%; /* Adjust the opacity for a subtler background */
        z-index: -1; /* Ensure the image is behind the content */
    }

    .header {
        background: linear-gradient(to right, #2c3e50, #10465a); /* Gradient background */
        color: white;
        padding: 20px 0;
        border-bottom: 2px solid #ddd;
    }

    .header a {
        color: white;
        font-size: 16px;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 5px;
        margin-left: 10px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .header a:hover {
        background-color: rgba(255, 255, 255, 0.15);
        transform: scale(1.05); /* Slight scaling effect for hover */
    }

    .company-logo {
        max-width: 60px;
        height: auto;
        margin-right: 15px;
    }

    .company-info {
        margin-top: 30px;
    }

    .company-info h2 {
        color: #10465a;
        font-size: 24px;
        margin-bottom: 15px;
    }

    .company-info p {
        font-size: 16px;
        color: #555;
    }

    .logout-icon {
        font-size: 24px;
        cursor: pointer;
    }

    .btn-logout {
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .btn-logout:hover {
        background-color: #c0392b;
        transform: scale(1.05); /* Slight scaling effect for hover */
    }

    .flight-table {
        margin-top: 40px;
    }

    .flight-table th {
        background-color: #2c3e50;
        color: white;
    }

    .flight-table td {
        font-size: 14px;
        color: #333;
    }

    .btn-view-flight {
        color: white;
        background-color: #3498db;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .btn-view-flight:hover {
        background-color: #2980b9;
        transform: scale(1.05); /* Slight scaling effect for hover */
    }

    .flight-card {
        background: #ffffff;
        padding: 20px;
        margin: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .flight-card h3 {
        color: #10465a;
    }

    .flight-card p {
        font-size: 14px;
        color: #333;
    }



    .form-container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0,0,0,0.1);
    }

    .form-group label {
        font-weight: bold;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .form-group input[type="file"] {
        padding: 5px;
    }

    .btn-update {
        background-color: #2ecc71;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-update:hover {
        background-color: #27ae60;
    }
</style>
<body>

<!-- Include the navbar -->
<div class="header">
    <div class="container d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <?php if ($company && isset($company['logo_img'])): ?>
                <img src="../images/<?= htmlspecialchars($company['logo_img']) ?>" alt="Company Logo" class="company-logo mr-3">
            <?php endif; ?>
            <h1><?= htmlspecialchars($company['name']) ?></h1>
        </div>
        <div>
            <a href="company_home.php" class="btn btn-info">Home <i class="fas fa-home"></i></a>
            <a href="?logout=true" class="btn-logout">Logout <i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <h2>Company Bio:</h2>
            <p><?= htmlspecialchars($company['bio']) ?></p>
        </div>
        <div class="col-md-6">
            <h2>Company Address:</h2>
            <p><?= htmlspecialchars($company['address']) ?></p>
        </div>
    </div>

<div class="container mt-5">
    <h2>Update Company Information</h2>
    <div class="form-container">
        <form action="company_profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Company Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($company['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="bio">Company Bio</label>
                <textarea id="bio" name="bio" rows="4" required><?= htmlspecialchars($company['bio']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="address">Company Address</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($company['address']) ?>" required>
            </div>
            <div class="form-group">
                <label for="logo_img">Company Logo</label>
                <input type="file" id="logo_img" name="logo_img">
            </div>
            <button type="submit" name="update" class="btn-update">Update Information</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="text-danger"><?= $error ?></p>
        <?php endif; ?>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
