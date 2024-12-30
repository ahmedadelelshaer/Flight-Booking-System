<?php
session_start();
require_once '../php/includes/db.php'; // Include your DB connection
require_once '../models/Flight.php'; // Assuming you have a Flight model file

$conn = connectToDB();
$flightModel = new Flight($conn);

// Check if the user is logged in and is of type 'company'
if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'company') {
    header("Location: login.php");
    exit();
}

// Check if the flight ID is provided
if (!isset($_GET['flight_id'])) {
    echo "<script>alert('No flight selected.'); window.location.href = 'company_home.php';</script>";
    exit();
}

$flightId = $_GET['flight_id'];

// Fetch flight details
$flight = $flightModel->getFlightDetails($flightId);

// Fetch pending passengers
$pendingPassengers = $flightModel->getPendingPassengers($flightId);

// Fetch registered passengers
$registeredPassengers = $flightModel->getRegisteredPassengers($flightId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../images/pexels-ahmedmuntasir-912050.jpg') no-repeat center center/cover;
            background-attachment: fixed;
            opacity: 30%;
            z-index: -1;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #10465a;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            color: white;
            font-size: 16px;
            text-decoration: none;
            margin-left: 15px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .details-container {
            margin-top: 40px;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .details-container h2 {
            color: #10465a;
            margin-bottom: 20px;
        }

        .details-container p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .card-header {
            background-color: #10465a;
            color: white;
            font-size: 18px;
        }

        .card-body {
            background-color: #ffffff;
        }

        .card {
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card .list-group-item {
            border: none;
            background-color: #f9f9f9;
        }

        .card .list-group-item:hover {
            background-color: #ebebeb;
        }

        .back-button {
            margin-top: 20px;
        }

        .btn-secondary {
            background-color: #10465a;
            color: white;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 16px;
            text-decoration: none;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-secondary:hover {
            background-color: #08353e;
            color: white;
        }

        a.btn-secondary {
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .details-container {
                margin-top: 20px;
                padding: 15px;
            }

            .btn-secondary {
                width: 100%;
                padding: 12px;
            }
        }

    </style>
</head>
<body>
<?php
// Assuming $flight contains the selected flight details as fetched earlier
if ($flight):
    // Handle empty or null transit value
    $transit = isset($flight['transit']) && !empty($flight['transit']) ? json_decode($flight['transit'], true) : [];
    $transitCities = is_array($transit) && !empty($transit) ? implode(', ', $transit) : 'No Transit';
    ?>
    <div class="container mt-4">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Flight: <?= htmlspecialchars($flight['name']) ?></h5>
                <p class="card-text"><strong>ID:</strong> <?= htmlspecialchars($flight['id']) ?></p>
                <p class="card-text"><strong>Source:</strong> <?= htmlspecialchars($flight['source']) ?></p>
                <p class="card-text"><strong>Destination:</strong> <?= htmlspecialchars($flight['destination']) ?></p>
                <strong>Transit Cities:</strong>
                <p class="card-text transit-column" data-raw="<?= htmlspecialchars($flight['transit']) ?>">
                    <?= htmlspecialchars($transitCities) ?>
                </p>
                <p class="card-text"><strong>Destination:</strong> <?= htmlspecialchars($flight['destination']) ?></p>
                <p class="card-text"><strong>Start Time:</strong> <?= htmlspecialchars($flight['start_datetime']) ?></p>
                <p class="card-text"><strong>End Time:</strong> <?= htmlspecialchars($flight['end_datetime']) ?></p>

                <!-- Pending Passengers List -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Pending Passengers</h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pendingPassengers)): ?>
                            <p>No pending passengers.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($pendingPassengers as $passenger): ?>
                                    <li class="list-group-item">
                                        <?= htmlspecialchars($passenger['name']) ?> (ID: <?= htmlspecialchars($passenger['id']) ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Registered Passengers List -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Registered Passengers</h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($registeredPassengers)): ?>
                            <p>No registered passengers.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($registeredPassengers as $passenger): ?>
                                    <li class="list-group-item">
                                        <?= htmlspecialchars($passenger['name']) ?> (ID: <?= htmlspecialchars($passenger['id']) ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <a href="company_home.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="container mt-4">
        <p>No flight details available.</p>
        <a href="company_home.php" class="btn btn-secondary">Back to Home</a>
    </div>
<?php endif; ?>

<script>
    // Function to sanitize transit values
    function sanitizeTransit(transit) {
        if (!transit) return 'No Transit'; // Handle empty or null values

        // Remove unwanted characters and return the cleaned string
        return transit.replace(/[\\"[\]]/g, '').trim();
    }

    // Apply the function to sanitize the transit column dynamically
    document.addEventListener('DOMContentLoaded', () => {
        const transitElement = document.querySelector('.transit-column');
        if (transitElement) {
            const rawValue = transitElement.getAttribute('data-raw');
            transitElement.textContent = sanitizeTransit(rawValue);
        }
    });
</script>
</body>
</html>
