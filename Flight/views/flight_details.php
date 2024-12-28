<?php
session_start();
require_once '../models/Flight.php'; // Assuming the Flight model is loaded

// Check if flight ID is provided in the query string
if (!isset($_GET['id'])) {
    die("Flight ID is required.");
}

$flightId = $_GET['id'];

// Simulating data retrieval (replace with actual database query)
$flightdata = new Flight();
$flight = $flightdata->getFlightDetails($flightId);

// Handle the flight "Take It?" action when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['take_flight']) && !empty($flightId)) {
        $userId = $_SESSION['id']; // Get the user ID from session

        // Try to add the flight to the user
        $flightTaken = $flightdata->addflighttouser($flightId, $userId);

        if ($flightTaken) {
            $flightStatusMessage = "You have successfully taken the flight!";
            $modalType = 'success';  // Success modal
        } else {
            $flightStatusMessage = "You have already taken this flight!";
            $modalType = 'danger';  // Error modal
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
        }

        .card {
            margin-top: 20px;
            border: none;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .alert {
            display: none;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <h2 class="text-center">Flight Details</h2>

        <div class="card">
            <div class="card-body">
                <h3 class="mb-3">Flight ID: <?php echo htmlspecialchars($flight['id']); ?></h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($flight['name']); ?></p>
                <p><strong>Itinerary:</strong> <?php echo htmlspecialchars($flight['transit']); ?></p>
                <p><strong>Fees:</strong> $<?php echo htmlspecialchars(number_format($flight['fees'], 2)); ?></p>
                <p><strong>Time:</strong>
                    <?php echo htmlspecialchars($flight['start_datetime'] . ' - ' . $flight['end_datetime']); ?></p>
                <p><strong>Status:</strong> <?php echo $flight['is_completed'] ? 'Completed' : 'Pending'; ?></p>

                <h4 class="mt-4">Passengers</h4>
                <ul>
                    <li><strong>Registered:</strong> <?php echo htmlspecialchars($flight['passengers_registered']); ?>
                    </li>
                    <li><strong>Pending:</strong> <?php echo htmlspecialchars($flight['passengers_pending']); ?></li>
                </ul>

                <?php if (!$flight['is_completed'] && !isset($flightStatusMessage)): ?>
                    <form method="POST">
                        <input type="hidden" name="take_flight" value="1">
                        <button type="submit" class="btn btn-danger mt-3">Take It?</button>
                    </form>
                <?php endif; ?>

                <!-- Back to Passenger Home Button -->
                <a href="passengerHome.php" class="btn btn-primary mt-3">Back to Home</a>
            </div>
        </div>
    </div>

    <!-- Modal for Success or Error Message -->
    <?php if (isset($flightStatusMessage)): ?>
        <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statusModalLabel">
                            <?php echo $modalType == 'success' ? 'Success' : 'Error'; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php echo $flightStatusMessage; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show the modal when the page loads
        <?php if (isset($flightStatusMessage)): ?>
            var myModal = new bootstrap.Modal(document.getElementById('statusModal'), {
                keyboard: false
            });
            myModal.show();
        <?php endif; ?>
    </script>
</body>

</html>