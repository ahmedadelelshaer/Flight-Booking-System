<?php


function get_passengers($sortOrder = 'ASC')
{
    $conn = connectToDB();
    if ($conn) {
        $stmt = $conn->prepare("SELECT * FROM passengers ORDER BY id $sortOrder");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return [];
    }
}
function registerPassenger($name, $email, $password, $tel, $account_number, $photo, $passportImg)
{
    $conn = connectToDB();

    // Check for existing email or name
    $stmt = $conn->prepare("SELECT * FROM passenger WHERE email = :email OR name = :name");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':name', $name);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return false; // Email or name already exists
    }

    // Validate photo for passengers
    if (empty($photo) or empty($passportImg)) {
        return false; // Passenger must provide a photo
    }
    // Insert passenger details
    $sql = "INSERT INTO passenger (name, email, password, tel, account_number, photo, passport_img) 
            VALUES (:name, :email, :password, :tel, :account_number, :photo, :passport_img)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':account_number', $account_number);
    $stmt->bindParam(':photo', $photo);
    $stmt->bindParam(':passport_img', $passportImg);
    $stmt->execute();
    return true;
}