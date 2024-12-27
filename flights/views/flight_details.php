<?php
session_start();
require_once 'models/Flight.php'; // Assuming the Flight model is loaded

// Retrieve the flight ID from the query string
$flightId = $_GET['id'];

// Sample data for this example, in a real-world scenario this would come from a database
$flight = new Flight($flightId, "Flight 101", "New York -> Paris -> Tokyo", 500, '2024-12-25 14:00', '2024-12-25 18:00');
$flight->addPassenger('registered');
$flight->addPassenger('pending');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background-color: #f4f4f9;">

    <div class="container mt-5">
        <h2 class="text-center">Flight Details</h2>

        <div class="card">
            <div class="card-body">
                <h3>ID: <?php echo $flight->id; ?></h3>
                <p><strong>Name:</strong> <?php echo $flight->name; ?></p>
                <p><strong>Itinerary:</strong> <?php echo $flight->itinerary; ?></p>
                <p><strong>Fees:</strong> $<?php echo $flight->fees; ?></p>
                <p><strong>Time:</strong> <?php echo $flight->getFlightTime(); ?></p>
                <p><strong>Status:</strong> <?php echo $flight->completed ? 'Completed' : 'Active'; ?></p>

                <h4>Passengers</h4>
                <ul>
                    <li><strong>Registered:</strong> <?php echo $flight->registeredPassengers; ?></li>
                    <li><strong>Pending:</strong> <?php echo $flight->pendingPassengers; ?></li>
                </ul>

                <?php if (!$flight->completed): ?>
                    <a href="cancel_flight.php?id=<?php echo $flight->id; ?>" class="btn btn-danger">Cancel Flight</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>