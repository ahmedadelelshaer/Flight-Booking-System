<?php
include_once '../php/includes/db.php';

class Flight
{
    private $conn;

    public function __construct()
    {
        $this->conn = connectToDB();
    }

    // Add a new flight
    public function addflighttouser($flightId, $passengerId)
    {
        // Check if the user has already taken this flight
        $sql = "SELECT COUNT(*) FROM passengers_flights WHERE flight_id = :flight_id AND passenger_id = :passenger_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['flight_id' => $flightId, 'passenger_id' => $passengerId]);

        // If the count is greater than 0, it means the user already has this flight
        if ($stmt->fetchColumn() > 0) {
            return false; // Return false if the user already has the flight
        }

        // Insert the new record if the user doesn't have it already
        $sql = "INSERT INTO passengers_flights (flight_id, passenger_id) VALUES (:flight_id, :passenger_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['flight_id' => $flightId, 'passenger_id' => $passengerId]);
        return true; // Return true if the insertion is successful
    }
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
            $flight['transit'] = json_decode($flight['transit'], true); // Decode JSON to array
        }

        return $flight;
    }
    // Get pending passengers for a flight


    public function updateFlight($flightId, $flightData)
    {
        $sql = "UPDATE flights SET 
                name = :name, 
                source = :source, 
                destination = :destination, 
                transit = :transit, 
                fees = :fees, 
                passenger_limit = :passenger_limit, 
                start_time = :start_time, 
                end_time = :end_time 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $flightData['transit'] = json_encode($flightData['transit']); // Convert transit array to JSON
        $flightData['id'] = $flightId;

        return $stmt->execute($flightData);
    }
    // Get registered passengers for a flight


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
        $sql = "UPDATE passenger SET account_number = :new_account_number WHERE id = :id";
        $stmt = $this->conn->prepare($sql)  ;
        $stmt->execute(['new_account_number' => $newAccountNumber, 'id' => $passengerId]);
    }
    public function processPaymentAndBookFlight($flightId, $userId, $paymentType)
    {
        if ($paymentType == 'cash') {
            $paymentSuccess = true;
            $this->addflighttouser($flightId, $userId);
            $sql = "UPDATE passengers_flights  SET status = 'pending' WHERE flight_id = :flightid And passenger_id = :passid ";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['flightid' => $flightId, 'passid' => $userId]);
            return true;

        } else {
            $sql="SELECT flights.fees ,passenger.account_number FROM flights, passenger WHERE flights.id = :flightid And passenger.id = :passid";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['flightid' => $flightId, 'passid' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $fees = $result['fees'];
            $account_number = $result['account_number'];
            if ($fees > $account_number) {
                $paymentSuccess= false;
            }
            else{
                $sql = "UPDATE passenger SET account_number = account_number - :fees WHERE id = :passid";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['fees' => $fees, 'passid' => $userId]);
                $paymentSuccess = true;
            }
            if ($paymentSuccess) {
                $this->addflighttouser($flightId, $userId);
                $sql = "UPDATE passengers_flights  SET status = 'registered' WHERE flight_id = :flightid And passenger_id = :passid ";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['flightid' => $flightId, 'passid' => $userId]);
                // Mark the flight as booked for the user
                $updateLimitSql = "UPDATE flights 
                   SET passenger_limit = passenger_limit - 1 
                   WHERE id = :flightid AND passenger_limit > 0";
                $limitStmt = $this->conn->prepare($updateLimitSql);
                $limitStmt->execute([':flightid' => $flightId]);

                $check_is_completedSql = "UPDATE flights 
                   SET is_completed = 1 
                   WHERE id = :flightid AND passenger_limit = 0";
                $checkStmt = $this->conn->prepare($check_is_completedSql);
                $checkStmt->execute([':flightid' => $flightId]);
                return true;
            }
        }
        return false;
    }
    public function getPendingPassengers($flightId)
    {
        $query = "SELECT p.id, p.name, pf.status FROM passenger p
                  JOIN passengers_flights pf ON p.id = pf.passenger_id
                  WHERE pf.flight_id = :flight_id AND pf.status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return as an associative array
    }

    // Method to fetch registered passengers
    public function getRegisteredPassengers($flightId)
    {
        $query = "SELECT p.id, p.name, pf.status FROM passenger p
                  JOIN passengers_flights pf ON p.id = pf.passenger_id
                  WHERE pf.flight_id = :flight_id AND pf.status = 'registered'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return as an associative array
    }
    public function updateFlightCompletionStatus($flightId)
    {
        // Fetch the flight details including the passenger limit and registered passengers count
        $stmt = $this->conn->prepare("
        SELECT passenger_limit, 
            (SELECT COUNT(*) 
             FROM passengers_flights pf 
             WHERE pf.flight_id = :flight_id AND pf.status = 'registered') AS registered_count 
        FROM flights 
        WHERE id = :flight_id
    ");
        $stmt->execute(['flight_id' => $flightId]);
        $flightData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($flightData) {
            $passengerLimit = $flightData['passenger_limit'];
            $registeredCount = $flightData['registered_count'];

            // Check if the passenger limit is reached
            if ($registeredCount >= $passengerLimit) {
                // Update the flight's is_complete field to 1 if the limit is reached
                $updateStmt = $this->conn->prepare("UPDATE flights SET is_complete = 1 WHERE id = :flight_id");
                $updateStmt->execute(['flight_id' => $flightId]);
                return true; // Successfully updated the flight completion status
            }
        }

        return false; // No update needed, passenger limit is not reached
    }


}

?>