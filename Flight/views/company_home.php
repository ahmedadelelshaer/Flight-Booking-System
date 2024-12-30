<?php
session_start();
include_once '../php/includes/db.php';
$conn = connectToDB();
include_once '../models/Flight.php'; // Make sure you include the model file

// Check if the user is logged in and is a company admin
if (!isset($_SESSION['id']) || $_SESSION['type'] != 'company') {
    header("Location: login.php");
    exit();
}

// Fetch company data
$companyId = $_SESSION['id'];
$stmt = $conn->prepare("SELECT name, bio, address, logo_img FROM company WHERE id = ?");
$stmt->execute([$companyId]);
$company = $stmt->fetch();

// Fetch flights for the company
$stmt = $conn->prepare("SELECT id, name, source, destination, start_datetime, end_datetime, transit FROM flights WHERE company_id = ?");
$stmt->execute([$companyId]);
$flights = $stmt->fetchAll();

// Logout logic
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Flight cancellation logic
if (isset($_POST['cancel_flight'])) {
    $flightId = $_POST['flight_id'];

    // Create an instance of the Flight model
    $flightModel = new Flight($conn); // Pass the database connection to the model

    // Call the cancelFlight method to handle the cancellation
    $cancelSuccess = $flightModel->cancelFlight($flightId);

    if ($cancelSuccess) {
        echo "<script>alert('Flight cancelled successfully!');</script>";
        header("Location: company_home.php"); // Redirect back to the company home page
        exit();
    } else {
        echo "<script>alert('Failed to cancel the flight.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Home</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
            position: fixed; /* Make the background stay fixed */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../images/pexels-ahmedmuntasir-912050.jpg') no-repeat center center/cover; /* Ensure the image covers the viewport */
            background-attachment: fixed; /* Keep the image fixed while scrolling */
            opacity: 25%; /* Adjust the opacity for a subtler background */
            z-index: -1; /* Ensure the image is behind the content */
        }

        .header {
            background: linear-gradient(to right, #2c3e50, #10465a); /* Gradient background */
            color: white;
            padding: 20px 0;
            border-bottom: 2px solid #ddd;
        }

        .header a {
            color: white;
            font-size: 16px;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            margin-left: 10px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .header a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: scale(1.05); /* Slight scaling effect for hover */
        }

        .company-logo {
            max-width: 60px;
            height: auto;
            margin-right: 15px;
        }

        .company-info {
            margin-top: 30px;
        }

        .company-info h2 {
            color: #10465a;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .company-info p {
            font-size: 16px;
            color: #555;
        }

        .logout-icon {
            font-size: 24px;
            cursor: pointer;
        }

        .btn-logout {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-logout:hover {
            background-color: #c0392b;
            transform: scale(1.05); /* Slight scaling effect for hover */
        }

        .flight-table {
            margin-top: 40px;
        }

        .flight-table th {
            background-color: #2c3e50;
            color: white;
        }

        .flight-table td {
            font-size: 14px;
            color: #333;
        }

        .btn-view-flight {
            color: white;
            background-color: #3498db;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-view-flight:hover {
            background-color: #2980b9;
            transform: scale(1.05); /* Slight scaling effect for hover */
        }

        .flight-card {
            background: #ffffff;
            padding: 20px;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .flight-card h3 {
            color: #10465a;
        }

        .flight-card p {
            font-size: 14px;
            color: #333;
        }


    </style>
</head>
<body>

<!-- Company Header -->
<div class="header">
    <div class="container d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <?php if ($company && isset($company['logo_img'])): ?>
                <img src="../images/<?= htmlspecialchars($company['logo_img']) ?>" alt="Company Logo" class="company-logo mr-3">
            <?php endif; ?>
            <h1><?= htmlspecialchars($company['name']) ?></h1>
        </div>
        <div>
            <!-- Profile link, Add Flight button, and Logout button -->
            <a href="company_profile.php" class="btn btn-info">Profile <i class="fas fa-user"></i></a>
            <a href="add_flight.php" class="btn btn-success">Add Flight <i class="fas fa-plane-departure"></i></a>
            <a href="?logout=true" class="btn-logout">Logout <i class="fas fa-sign-out-alt"></i></a>
            <a href="notifications.php" class="btn btn-warning"><i class="fas fa-bell"></i></a>
        </div>
    </div>
</div>

<div class="container company-info">
    <div class="row">
        <div class="col-md-6">
            <h2>Company Bio:</h2>
            <p><?= htmlspecialchars($company['bio']) ?></p>
        </div>
        <div class="col-md-6">
            <h2>Address:</h2>
            <p><?= htmlspecialchars($company['address']) ?></p>
        </div>
    </div>

    <h2>Flights</h2>
    <?php if (count($flights) > 0): ?>
        <div class="flight-table">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Source</th>
                    <th>Destination</th>
                    <th>Transit</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($flights as $flight): ?>
                    <?php
                    // Handle empty or null transit value
                    $transit = isset($flight['transit']) && !empty($flight['transit']) ? json_decode($flight['transit'], true) : [];

                    if (is_string($transit)) {
                        $transitCities = $transit;
                    } else {
                        $transitCities = !empty($transit) ? implode(', ', $transit) : 'No Transit';
                    }
                    ?>

                    <tr>
                        <td><?= htmlspecialchars($flight['id']) ?></td>
                        <td><?= htmlspecialchars($flight['name']) ?></td>
                        <td><?= htmlspecialchars($flight['source']) ?></td>
                        <td><?= htmlspecialchars($flight['destination']) ?></td>
                        <td class="transit-column" data-raw="<?= htmlspecialchars($flight['transit']) ?>"><?= $transitCities ?></td>
                        <td><?= htmlspecialchars($flight['start_datetime']) ?></td>
                        <td><?= htmlspecialchars($flight['end_datetime']) ?></td>
                        <td>
                            <a href="flight_details_comp.php?flight_id=<?= htmlspecialchars($flight['id']) ?>" class="btn btn-primary btn-view-flight">View Details</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="flight_id" value="<?= htmlspecialchars($flight['id']) ?>">
                                <button type="submit" name="cancel_flight" class="btn btn-danger btn-cancel-flight">Cancel</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No flights available.</p>
    <?php endif; ?>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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