<?php
class Flight
{
    private $conn;

    public function __construct()
    {
        $this->conn = connectToDB();
    }

    // Add a new flight
    public function addFlight($name, $source, $destination, $transit, $fees, $passengerLimit, $start_datetime, $end_datetime)
    {
        if (!isset($_SESSION['company_id'])) {
            echo "Company ID not found in session. Please log in.";
            exit();
        }

        $companyId = $_SESSION['company_id'];

        $stmt = $this->conn->prepare("SELECT id FROM company WHERE id = :company_id");
        $stmt->execute(['company_id' => $companyId]);
        $companyExists = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$companyExists) {
            echo "The specified company does not exist!";
            exit();
        }

        $sql = "INSERT INTO flights (name, source, destination, transit, fees, passenger_limit, start_datetime, end_datetime, company_id) 
                VALUES (:name, :source, :destination, :transit, :fees, :passenger_limit, :start_datetime, :end_datetime, :company_id)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'source' => $source,
            'destination' => $destination,
            'transit' => json_encode($transit),
            'fees' => $fees,
            'passenger_limit' => $passengerLimit,
            'start_datetime' => $start_datetime,
            'end_datetime' => $end_datetime,
            'company_id' => $companyId
        ]);

        return $this->conn->lastInsertId();
    }

    // Get flight details by ID
    public function getFlightDetails($flightId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM flights WHERE id = :id");
        $stmt->execute(['id' => $flightId]);
        $flight = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($flight) {
            $flight['transit'] = json_decode($flight['transit'], true);
        }

        return $flight;
    }

    // Get pending passengers for a flight
    public function getPendingPassengers($flightId)
    {
        // Modify the query to join the passengers_flights table and filter by status
        $stmt = $this->conn->prepare("
            SELECT p.* 
            FROM passenger p
            JOIN passengers_flights pf ON p.id = pf.passenger_id
            WHERE pf.flight_id = :flight_id AND p.status = 'pending'
        ");
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get registered passengers for a flight
    public function getRegisteredPassengers($flightId)
    {
        // Modify the query to join the passengers_flights table and filter by status
        $stmt = $this->conn->prepare("
            SELECT p.* 
            FROM passenger p
            JOIN passengers_flights pf ON p.id = pf.passenger_id
            WHERE pf.flight_id = :flight_id AND p.status = 'registered'
        ");
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cancel a flight and refund fees to passengers
    public function cancelFlight($flightId)
    {
        // Fetch passengers associated with the flight from the passengers_flights table
        $passengerStmt = $this->conn->prepare("
        SELECT p.id, p.account_number 
        FROM passenger p
        JOIN passengers_flights pf ON p.id = pf.passenger_id
        WHERE pf.flight_id = :flight_id
    ");
        $passengerStmt->execute(['flight_id' => $flightId]);
        $passengers = $passengerStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch the flight fees
        $flightStmt = $this->conn->prepare("SELECT fees FROM flights WHERE id = :flight_id");
        $flightStmt->execute(['flight_id' => $flightId]);
        $flight = $flightStmt->fetch(PDO::FETCH_ASSOC);

        if ($flight) {
            $fees = $flight['fees'];  // Fees to be added to account_number

            foreach ($passengers as $passenger) {
                // Add flight fees to the passenger's account_number
                $this->refundPassenger($passenger['id'], $passenger['account_number'], $fees);
            }

            // Delete the flight
            $stmt = $this->conn->prepare("DELETE FROM flights WHERE id = :id");
            $stmt->execute(['id' => $flightId]);

            return $stmt->rowCount() > 0;
        }

        return false;
    }

    private function refundPassenger($passengerId, $currentAccountNumber, $fees)
    {
        // Add flight fees to the passenger's account_number
        $newAccountNumber = $currentAccountNumber + $fees;
        $stmt = $this->conn->prepare("UPDATE passengers SET account_number = :new_account_number WHERE id = :id");
        $stmt->execute(['new_account_number' => $newAccountNumber, 'id' => $passengerId]);
    }

  
}

?>
