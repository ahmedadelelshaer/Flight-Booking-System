<?php
// Flight Model to represent flights
class Flight
{
    public $id;
    public $name;
    public $itinerary; // Cities to pass through
    public $registeredPassengers = 0;
    public $pendingPassengers = 0;
    public $fees;
    public $startTime;
    public $endTime;
    public $completed = false;

    public function __construct($id, $name, $itinerary, $fees, $startTime, $endTime)
    {
        $this->id = $id;
        $this->name = $name;
        $this->itinerary = $itinerary;
        $this->fees = $fees;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    // Method to add passengers
    public function addPassenger($status)
    {
        if ($status == 'registered') {
            $this->registeredPassengers++;
        } elseif ($status == 'pending') {
            $this->pendingPassengers++;
        }
    }

    // Method to cancel the flight and return fees to passengers
    public function cancelFlight()
    {
        $this->completed = true;
        // Logic to send money back to passengers (this can be added based on your payment system)
    }

    // Method to get formatted flight time
    public function getFlightTime()
    {
        return "From: " . date('Y-m-d H:i', strtotime($this->startTime)) . " To: " . date('Y-m-d H:i', strtotime($this->endTime));
    }
}
?>