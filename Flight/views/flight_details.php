<?php
session_start();
require_once '../models/Flight.php'; // Assuming the Flight model is loaded
require_once '../models/MessageModel.php'; // Assuming the Flight model is loaded

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
    if (isset($_POST['send_msg']) && !empty($flightId)) {
        $userId = $_SESSION['id']; // Get the user ID from session
        $messageContent = $_POST['message']; // Get the message content

        $messageModel = new MessageModel();
        $date = date('Y-m-d H:i:s');
        $result = $messageModel->Send_Message($userId, $flightId, $messageContent,$date);

        if ($result) {
            $flightStatusMessage = "Message sent successfully!";
            $modalType = 'success';  // Success modal
        } else {
            $flightStatusMessage = "Failed to send message!";
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
            font-family: 'Poppins', sans-serif;
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
            margin-top: 20px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #10465a;
        }

        .card-text {
            font-size: 1rem;
            line-height: 1.6;
        }

        .btn {
            font-size: 1rem;
            font-weight: 500;
            border-radius: 8px;
            transition: transform 0.2s, background-color 0.3s;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        .btn-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }

        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .modal-header {
            background-color: #10465a;
            color: white;
        }

        .modal-title {
            font-size: 1.5rem;
        }

        .btn-close {
            background-color: white;
            color: #10465a;
            border-radius: 50%;
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

            <a href="company_home.php" class="btn btn-secondary">Back to Home</a>
            <?php if (!$flight['is_completed'] && !isset($flightStatusMessage)): ?>
                <form method="POST" action="paymentSelection.php?id=<?php echo htmlspecialchars($flightId); ?>">
                    <input type="hidden" name="take_flight" value="1">
                    <button type="submit" class="btn btn-danger mt-3">Take It?</button>
                </form>
            <?php endif; ?>
            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#messageModal">
                Send Message
            </button>
        </div>
    </div>
</div>

<?php endif; ?>
<!-- Modal for Sending Message -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Send a Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="flight_details.php?id=<?php echo htmlspecialchars($flightId); ?>">
                    <input type="hidden" name="send_msg" value="1">
                    <div class="mb-3">
                        <label for="messageContent" class="form-label">Your Message</label>
                        <textarea class="form-control" id="messageContent" name="message" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
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