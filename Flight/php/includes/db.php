<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function connectToDB()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "flight_booking1";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Print a success message
        // echo "Database connected successfully!";
        return $conn;
    } catch (PDOException $e) {
        // Print an error message if the connection fails
        // echo "nothing";
    }
}

?>