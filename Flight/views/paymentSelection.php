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

// Handle form submission for payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['payment_type'])) {
        $paymentType = $_POST['payment_type'];
        $userId = $_SESSION['id'];

        // Process payment and booking
        $paymentSuccess = $flightdata->processPaymentAndBookFlight($flightId, $userId, $paymentType);

        if ($paymentSuccess) {
            $flightStatusMessage = "You have successfully taken Flight ID $flightId using $paymentType payment.";
            $modalType = 'success'; // Success modal
        } else {
            $flightStatusMessage = "Payment failed. Please try again.(Insufficient balance)";
            $modalType = 'danger'; // Error modal
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

        .btn-payment {
            width: 100%;
            margin-top: 10px;
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
            <p class="card-text"><?= htmlspecialchars($transitCities) ?></p>
            <p class="card-text"><strong>Start Time:</strong> <?= htmlspecialchars($flight['start_datetime']) ?></p>
            <p class="card-text"><strong>End Time:</strong> <?= htmlspecialchars($flight['end_datetime']) ?></p>
           
                <?php if (!$flight['is_completed'] && !isset($flightStatusMessage)): ?>
                    <form method="POST">
                        <h5>Select Payment Method:</h5>
                        <button type="submit" name="payment_type" value="cash" class="btn btn-danger btn-payment">Pay with Cash</button>
                        <button type="submit" name="payment_type" value="account" class="btn btn-primary btn-payment">Pay with Account</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
                <!-- Back to Passenger Home Button -->
                <a href="passengerHome.php" class="btn btn-dark mt-3">Back to Home</a>
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