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
<<<<<<< HEAD
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
            <a href="company_home.php" class="btn btn-secondary">Back to Home</a>
            <?php if (!$flight['is_completed'] && !isset($flightStatusMessage)): ?>
                <form method="POST" action="paymentSelection.php?id=<?php echo htmlspecialchars($flightId); ?>">
                    <input type="hidden" name="take_flight" value="1">
                    <button type="submit" class="btn btn-danger mt-3">Take It?</button>
                </form>
            <?php endif; ?>
=======

    <div class="container mt-5">
        <h2 class="text-center">Flight Details</h2>

        <div class="card">
            <div class="card-body">
                <h3 class="mb-3">Flight ID: <?php echo htmlspecialchars($flight['id']); ?></h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($flight['name']); ?></p>
                <p><strong>Transit:</strong> <?php echo htmlspecialchars($flight['transit']); ?></p>
                <td class="transit-column" data-raw="<?= htmlspecialchars($flight['transit']) ?>"><?= $transitCities ?></td>
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
                    <form method="POST" action="paymentSelection.php?id=<?php echo htmlspecialchars($flightId); ?>">
                        <input type="hidden" name="take_flight" value="1">
                        <button type="submit" class="btn btn-danger mt-3">Take It?</button>
                    </form>
                <?php endif; ?>

                <!-- Back to Passenger Home Button -->
                <a href="passengerHome.php" class="btn btn-primary mt-3">Back to Home</a>
            </div>
>>>>>>> 59638d73e1ba93e4e8101720324e050d1a759eb7
        </div>
    </div>
</div>
<?php endif; ?>
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

<<<<<<< HEAD
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
=======
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
>>>>>>> 59638d73e1ba93e4e8101720324e050d1a759eb7
    // Function to sanitize transit values
    function sanitizeTransit(transit) {
        if (!transit) return 'No Transit'; // Handle empty or null values

        // Remove unwanted characters and return the cleaned string
        return transit.replace(/[\\"[\]]/g, '').trim();
    }

    // Apply the function to sanitize transit columns dynamically
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.transit-column').forEach(cell => {
            const rawValue = cell.getAttribute('data-raw');
            cell.textContent = sanitizeTransit(rawValue);
        });
    });
</script>
</body>

</html>