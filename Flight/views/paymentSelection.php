<?php
session_start();
require_once '../models/Flight.php'; // Assuming the Flight model is loaded

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
            opacity: 0.3;
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

        .card {
            margin: 20px auto;
            max-width: 600px;
            border: none;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-size: 24px;
            font-weight: bold;
            color: #10465a;
        }

        .card-text {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .btn-payment {
            width: 100%;
            margin-top: 15px;
            font-size: 16px;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-payment:hover {
            opacity: 0.9;
        }

        .btn-danger {
            background-color: #e43d12;
            border: none;
        }

        .btn-primary {
            background-color: #10465a;
            border: none;
        }

        .btn-dark {
            background-color: #333;
            border: none;
        }

        .btn-dark:hover {
            background-color: #555;
        }

        .btn-back {
            margin-top: 20px;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-title {
            color: #10465a;
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