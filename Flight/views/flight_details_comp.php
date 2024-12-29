<?php
session_start();
require_once '../php/includes/db.php';

$conn = connectToDB();

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
$stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
$stmt->execute([$flightId]);
$flight = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flight) {
    echo "<script>alert('Flight not found.'); window.location.href = 'company_home.php';</script>";
    exit();
}

// Decode transit cities
$transit = !empty($flight['transit']) ? json_decode($flight['transit'], true) : [];
$transitCities = is_array($transit) ? implode(', ', $transit) : 'No Transit';
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
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
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

        .back-button {
            margin-top: 20px;
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
