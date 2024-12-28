<?php

class FlightModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = connectToDB();
    }

    // Add a new flight
    public function addFlight($name, $source, $destination, $transit, $fees, $passengerLimit, $startTime, $endTime, $companyId)
    {
        $sql = "INSERT INTO flights (name, source, destination, transit, fees, passenger_limit, start_time, end_time, company_id) 
                VALUES (:name, :source, :destination, :transit, :fees, :passenger_limit, :start_time, :end_time, :company_id)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            'name' => $name,
            'source' => $source,
            'destination' => $destination,
            'transit' => json_encode($transit), // Convert transit array to JSON
            'fees' => $fees,
            'passenger_limit' => $passengerLimit,
            'start_time' => $startTime,
            'end_time' => $endTime,
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
            $flight['transit'] = json_decode($flight['transit'], true); // Decode transit JSON back to array
        }

        return $flight;
    }

    // Get pending passengers for a flight
    public function getPendingPassengers($flightId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM passengers WHERE flight_id = :flight_id AND status = 'pending'");
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get registered passengers for a flight
    public function getRegisteredPassengers($flightId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM passengers WHERE flight_id = :flight_id AND status = 'registered'");
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cancel a flight and refund fees to passengers
    public function cancelFlight($flightId)
    {
        // Fetch passengers to refund fees
        $passengerStmt = $this->conn->prepare("SELECT id, fees_paid FROM passengers WHERE flight_id = :flight_id");
        $passengerStmt->execute(['flight_id' => $flightId]);
        $passengers = $passengerStmt->fetchAll(PDO::FETCH_ASSOC);

        // Refund fees logic (you can integrate payment API here)
        foreach ($passengers as $passenger) {
            $this->refundPassenger($passenger['id'], $passenger['fees_paid']);
        }

        // Delete flight
        $stmt = $this->conn->prepare("DELETE FROM flights WHERE id = :id");
        $stmt->execute(['id' => $flightId]);

        return $stmt->rowCount() > 0;
    }

    private function refundPassenger($passengerId, $fees)
    {
        // Logic to refund a passenger (dummy function for now)
        // e.g., update their account balance
        $stmt = $this->conn->prepare("UPDATE passengers SET account_balance = account_balance + :fees WHERE id = :id");
        $stmt->execute(['fees' => $fees, 'id' => $passengerId]);
    }
}