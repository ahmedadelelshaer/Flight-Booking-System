<?php
session_start();
require_once '../models/Flight.php'; // Assuming you have this in your models folder

// Sample data
$companyName = 'Awesome Airlines';
$companyLogo = 'logo.png'; // Path to the company logo image
$flights = [
    new Flight(1, "Flight 101", "New York -> Paris -> Tokyo", 500, '2024-12-25 14:00', '2024-12-25 18:00'),
    new Flight(2, "Flight 102", "London -> Dubai -> Sydney", 600, '2024-12-26 10:00', '2024-12-26 16:00')
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Home - Flight Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background-color: #f4f4f9;">

    <div class="container mt-5">
        <!-- Company Logo and Name -->
        <div class="text-center">
            <img src="images/<?php echo $companyLogo; ?>" alt="Logo" class="img-fluid" style="max-width: 150px;">
            <h1 class="mt-3"><?php echo $companyName; ?></h1>
        </div>

        <!-- Flights Section -->
        <div class="mt-5">
            <h2>Flight List</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Itinerary</th>
                        <th>#Registered / #Pending</th>
                        <th>Fees</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flights as $flight): ?>
                        <tr>
                            <td><?php echo $flight->id; ?></td>
                            <td><?php echo $flight->name; ?></td>
                            <td><?php echo $flight->itinerary; ?></td>
                            <td><?php echo $flight->registeredPassengers . " / " . $flight->pendingPassengers; ?></td>
                            <td>$<?php echo $flight->fees; ?></td>
                            <td><?php echo $flight->getFlightTime(); ?></td>
                            <td><?php echo $flight->completed ? 'Completed' : 'Active'; ?></td>
                            <td>
                                <a href="flight_details.php?id=<?php echo $flight->id; ?>" class="btn btn-info btn-sm">View
                                    Details</a>
                                <?php if (!$flight->completed): ?>
                                    <a href="cancel_flight.php?id=<?php echo $flight->id; ?>"
                                        class="btn btn-danger btn-sm">Cancel Flight</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="add_flight.php" class="btn btn-primary">Add New Flight</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>