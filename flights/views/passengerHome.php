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
$stmt = $conn->prepare("SELECT name, email, photo,passport_img tel FROM user WHERE id = ?");
$stmt->execute([$userId]);
$profile = $stmt->fetch();

// Fetch completed flights
$stmt = $conn->prepare("SELECT id, start_datetime,start_datetime, itenerary FROM flights WHERE passengers_registered = ? And is_completed = 1");
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
            margin: 0 auto;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
        }

        .profile-section {
            display: flex;
            align-items: center;
            margin-top: 30px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
        }

        .profile-details {
            flex: 1;
        }

        .profile-details h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .profile-details p {
            margin: 5px 0;
            font-size: 1rem;
        }

        .section-title {
            font-size: 1.25rem;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        .flights-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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

        .search-form {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .search-form input {
            padding: 10px;
            width: 70%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .search-form button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #218838;
        }

        .logout-btn {
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h1>Welcome to your Passenger Dashboard, <?php echo $profile['name']; ?>!</h1>
        </div>

        <!-- Profile Section -->
        <div class="profile-section">
            <img src="<?php echo $profile['image']; ?>" alt="Profile Image" class="profile-img">
            <div class="profile-details">
                <h2><?php echo $profile['name']; ?></h2>
                <p>Email: <?php echo $profile['email']; ?></p>
                <p>Phone: <?php echo $profile['tel']; ?></p>
            </div>
        </div>

        <!-- Completed Flights -->
        <div class="completed-flights">
            <h3 class="section-title">Completed Flights</h3>
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
                            <td><?php echo $flight['flight_no']; ?></td>
                            <td><?php echo $flight['flight_date']; ?></td>
                            <td><?php echo $flight['destination']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Current Flights -->
        <div class="current-flights">
            <h3 class="section-title">Current Flights</h3>
            <table class="flights-table">
                <thead>
                    <tr>
                        <th>Flight No.</th>
                        <th>Date</th>
                        <th>Destination</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currentFlights as $flight): ?>
                        <tr>
                            <td><?php echo $flight['flight_no']; ?></td>
                            <td><?php echo $flight['flight_date']; ?></td>
                            <td><?php echo $flight['destination']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Search Flight Section -->
        <div class="search-form">
            <input type="text" placeholder="Search for a flight" id="flightSearchFrom" name="from">
            <input type="text" placeholder="To" id="flightSearchTo" name="to">
            <button type="button" onclick="searchFlight()">Search</button>
        </div>

        <!-- Logout -->
        <button class="logout-btn" onclick="window.location.href='?logout=true'">Logout</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchFlight() {
            const from = document.getElementById('flightSearchFrom').value;
            const to = document.getElementById('flightSearchTo').value;
            if (from && to) {
                window.location.href = 'searchResults.php?from=' + from + '&to=' + to;
            } else {
                alert('Please fill in both fields!');
            }
        }
    </script>
</body>

</html>