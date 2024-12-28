<?php
include_once("../php/includes/db.php");
class Flight
{
    private $conn;

    public function __construct()
    {
        $this->conn = connectToDB(); // Assuming a database connection function exists
    }

    /**
     * Add a new flight to the database.
     *
     * @param string $name
     * @param string $source
     * @param string $destination
     * @param array $transit
     * @param float $fees
     * @param int $passengerLimit
     * @param string $startTime
     * @param string $endTime
     * @param int $companyId
     * @return int ID of the newly inserted flight
     */
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
    public function addFlight($name, $source, $destination, $transit, $fees, $passengerLimit, $startTime, $endTime, $companyId)
    {
        $sql = "INSERT INTO flights 
                (name, source, destination, transit, fees, passenger_limit, start_time, end_time, company_id) 
                VALUES (:name, :source, :destination, :transit, :fees, :passenger_limit, :start_time, :end_time, :company_id)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            'name' => $name,
            'source' => $source,
            'destination' => $destination,
            'transit' => json_encode($transit), // Convert transit array to JSON format
            'fees' => $fees,
            'passenger_limit' => $passengerLimit,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'company_id' => $companyId
        ]);

        return $this->conn->lastInsertId(); // Return the newly inserted flight ID
    }

    /**
     * Get flight details by ID.
     *
     * @param int $flightId
     * @return array|null Flight details or null if not found
     */
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

    /**
     * Get a list of pending passengers for a specific flight.
     *
     * @param int $flightId
     * @return array List of pending passengers
     */
    public function getPendingPassengers($flightId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM passengers WHERE flight_id = :flight_id AND status = 'pending'");
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a list of registered passengers for a specific flight.
     *
     * @param int $flightId
     * @return array List of registered passengers
     */
    public function getRegisteredPassengers($flightId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM passengers WHERE flight_id = :flight_id AND status = 'registered'");
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cancel a flight and refund fees to passengers.
     *
     * @param int $flightId
     * @return bool True if flight is successfully canceled, false otherwise
     */
    public function cancelFlight($flightId)
    {
        // Fetch passengers to refund fees
        $passengerStmt = $this->conn->prepare("SELECT id, fees_paid FROM passengers WHERE flight_id = :flight_id");
        $passengerStmt->execute(['flight_id' => $flightId]);
        $passengers = $passengerStmt->fetchAll(PDO::FETCH_ASSOC);

        // Refund fees logic
        foreach ($passengers as $passenger) {
            $this->refundPassenger($passenger['id'], $passenger['fees_paid']);
        }

        // Delete the flight
        $stmt = $this->conn->prepare("DELETE FROM flights WHERE id = :id");
        $stmt->execute(['id' => $flightId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Refund fees to a passenger.
     *
     * @param int $passengerId
     * @param float $fees
     * @return void
     */
    private function refundPassenger($passengerId, $fees)
    {
        $stmt = $this->conn->prepare("UPDATE passengers SET account_balance = account_balance + :fees WHERE id = :id");
        $stmt->execute(['fees' => $fees, 'id' => $passengerId]);
    }

    /**
     * Update flight details.
     *
     * @param int $flightId
     * @param array $flightData
     * @return bool True if the update was successful, false otherwise
     */
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

    /**
     * Delete a flight by ID.
     *
     * @param int $flightId
     * @return bool True if the flight was deleted, false otherwise
     */
    public function deleteFlight($flightId)
    {
        $stmt = $this->conn->prepare("DELETE FROM flights WHERE id = :id");
        $stmt->execute(['id' => $flightId]);

        return $stmt->rowCount() > 0;
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
