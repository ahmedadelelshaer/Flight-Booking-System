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
$stmt = $conn->prepare("SELECT name, email,account_number, photo, passport_img, tel FROM passenger WHERE id = ?");
$stmt->execute([$userId]);
$profile = $stmt->fetch();

// Fetch completed flights
$stmt = $conn->prepare("SELECT flights.name, flights.source, flights.destination, flight_id 
                        FROM passengers_flights 
                        JOIN flights ON flight_id = flights.id 
                        WHERE passenger_id = ? AND is_completed = 1");
$stmt->execute([$userId]);
$completedFlights = $stmt->fetchAll();

// Fetch current flights
$stmt = $conn->prepare("SELECT flights.name, flights.source, flights.destination, flight_id 
                        FROM passengers_flights 
                        JOIN flights ON flight_id = flights.id 
                        WHERE passenger_id = ? AND is_completed = 0");
$stmt->execute([$userId]);
$currentFlights = $stmt->fetchAll();

// Search flights functionality
$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['from'], $_GET['to'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];
    $stmt = $conn->prepare("SELECT id AS flight_id,name, company_id, source, destination 
                            FROM flights 
                            WHERE source LIKE ? AND destination LIKE ? AND is_completed = 0");
    $stmt->execute(["%$from%", "%$to%"]);
    $searchResults = $stmt->fetchAll();
}

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
            display: block;
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
            cursor: pointer;
        }

        .flights-table th {
            background-color: #007bff;
            color: white;
        }

        .flights-table tr:hover {
            background-color: #f1f1f1;
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
            <h1>Welcome to your Passenger Dashboard, <?php echo htmlspecialchars($profile['name']); ?>!</h1>
        </div>

        <!-- Profile Section (Row) -->
        <div class="row profile-section">
            <div class="col-md-4">
                <img src="<?php echo '../images/' . htmlspecialchars($profile['photo']); ?>" alt="Profile Image"
                    class="profile-img img-fluid">
            </div>
            <div class="col-md-8">
                <h2><?php echo htmlspecialchars($profile['name']); ?></h2>
                <p>Email: <?php echo htmlspecialchars($profile['email']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($profile['tel']); ?></p>
                <p>Balance <?php echo htmlspecialchars($profile['account_number']);?>   </p>
            </div>
        </div>

        <!-- Completed Flights Section (Row) -->
        <div class="row completed-flights">
            <h3 class="section-title">Completed Flights</h3>
            <?php if (count($completedFlights) > 0): ?>
                <table class="flights-table table table-bordered">
                    <thead>
                        <tr>
                            <th>Flight No.</th>
                            <th>Source</th>
                            <th>Destination</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($completedFlights as $flight): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($flight['name']); ?></td>
                                <td><?php echo htmlspecialchars($flight['source']); ?></td>
                                <td><?php echo htmlspecialchars($flight['destination']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="placeholder-text">No completed flights found.</p>
            <?php endif; ?>
        </div>

        <!-- Current Flights Section (Row) -->
        <div class="row completed-flights">
            <h3 class="section-title">Current Flights</h3>
            <?php if (count($currentFlights) > 0): ?>
                <table class="flights-table table table-bordered">
                    <thead>
                        <tr>
                            <th>Flight No.</th>
                            <th>Source</th>
                            <th>Destination</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($currentFlights as $flight): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($flight['name']); ?></td>
                                <td><?php echo htmlspecialchars($flight['source']); ?></td>
                                <td><?php echo htmlspecialchars($flight['destination']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="placeholder-text">No current flights found.</p>
            <?php endif; ?>
        </div>

        <!-- Search Section (Row) -->
        <div class="row search-section">
            <h3 class="section-title">Search Flights</h3>
            <form method="GET">
                <div class="col-md-4">
                    <input type="text" name="from" placeholder="From"
                        value="<?php echo isset($_GET['from']) ? htmlspecialchars($_GET['from']) : ''; ?>" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="to" placeholder="To"
                        value="<?php echo isset($_GET['to']) ? htmlspecialchars($_GET['to']) : ''; ?>" required>
                </div>
                <button type="submit" class="btn btn-success col-md-2">Search</button>
            </form>

            <?php if (!empty($searchResults)): ?>
                <h4 class="section-title mt-4">Search Results</h4>
                <table class="flights-table table table-bordered">
                    <thead>
                        <tr>
                            <th>Flight No.</th>
                            <th>Source</th>
                            <th>Destination</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($searchResults as $flight): ?>
                            <tr onclick="window.location.href='flight_details.php?id=<?php echo $flight['flight_id']; ?>'">
                                <td><?php echo htmlspecialchars($flight['name']); ?></td>
                                <td><?php echo htmlspecialchars($flight['source']); ?></td>
                                <td><?php echo htmlspecialchars($flight['destination']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'GET'): ?>
                <p class="placeholder-text">No flights found for your search criteria.</p>
            <?php endif; ?>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>