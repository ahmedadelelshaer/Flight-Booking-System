<?php
session_start();
include_once '../php/includes/db.php';
$conn = connectToDB();


// Check if user is logged in and is a passenger
if (!isset($_SESSION['id']) || $_SESSION['type'] != 'passenger') {
    header("Location: login.php");
    exit();
}

// Fetch passenger data
$userId = $_SESSION['id'];
$stmt = $conn->prepare("SELECT name, email, photo,passport_img, tel FROM passenger WHERE id = ?");
$stmt->execute([$userId]);
$profile = $stmt->fetch();

// Fetch completed flights
$stmt = $conn->prepare("SELECT id, start_datetime,start_datetime FROM flights WHERE passengers_registered = ? And is_completed = 1");
$stmt->execute([$userId]);
$completedFlights = $stmt->fetchAll();
// Logout logic
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
        }

        .navbar {
            background-color: #007bff;
            padding: 15px;
            border-radius: 10px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .profile-section {
            display: flex;
            align-items: center;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #007bff;
        }

        .flights-table {
            width: 100%;
            border-collapse: collapse;
        }

        .flights-table th,
        .flights-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .flights-table th {
            background-color: #007bff;
            color: white;
        }

        .search-section {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .search-section input {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .search-section button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-section button:hover {
            background-color: #218838;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        .placeholder-text {
            color: #777;
            font-size: 1rem;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <div class="navbar">    
        <a href="profile.php" class="profile-btn">Profile</a>
        <a href="#">Flights</a>
        <a href="?logout=true" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <div class="header">
            <h1>Welcome to your Passenger Dashboard, <?php echo $profile['name']; ?>!</h1>
        </div>

        <!-- Profile Section -->
        <div class="profile-section">
            <img src="<?php echo '../images/' . $profile['photo']; ?>" alt="Profile Image" class="profile-img">
            <img src="<?php echo '../images/'. $profile['passport_img']; ?>" alt="Passport Image" class="profile-img">
            <div class="profile-details">
                <h2><?php echo $profile['name']; ?></h2>
                <p>Email: <?php echo $profile['email']; ?></p>
                <p>Phone: <?php echo $profile['tel']; ?></p>
            </div>
        </div>

        <!-- Completed Flights -->
        <div class="completed-flights">
            <h3 class="section-title">Completed Flights</h3>
            <?php if (count($completedFlights) > 0): ?>
                <table class="flights-table">
                    <thead>
                        <tr>
                            <th>Flight No.</th>
                            <th>Date</th>
                            <th>Destination</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($completedFlights as $flight): ?>
                            <tr>
                                <td><?php echo $flight['id']; ?></td>
                                <td><?php echo $flight['start_datetime']; ?></td>
                                <td><?php echo $flight['destination']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="placeholder-text">No completed flights found.</p>
            <?php endif; ?>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <h3 class="section-title">Search Flights</h3>
            <form action="searchResults.php" method="GET">
                <input type="text" name="from" placeholder="From" required>
                <input type="text" name="to" placeholder="To" required>
                <button type="submit">Search</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
