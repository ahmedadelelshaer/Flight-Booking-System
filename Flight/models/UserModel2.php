<?php

include_once '../php/includes/db.php';



function get_company($sortOrder = 'ASC')
{
    $conn = connectToDB();
    if ($conn) {
        $stmt = $conn->prepare("SELECT * FROM company ORDER BY id $sortOrder");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return [];
    }
}
function login($email, $password)
{
    $conn = connectToDB();
    $stmt = $conn->prepare("SELECT * FROM passenger WHERE email = :email AND password = :password");
    $stmt->execute(['email' => $email, 'password' => $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($stmt->rowCount() == 0) {
        $conn = connectToDB();
        $stmt = $conn->prepare("SELECT * FROM company WHERE email = :email AND password = :password");
        $stmt->execute(['email' => $email, 'password' => $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return $user;
}




function registerCompany($name, $email, $password, $tel, $account_number, $bio = '', $address = '', $logoImg = '')
{
    $conn = connectToDB();

    // Check for existing email or name
    $stmt = $conn->prepare("SELECT * FROM company WHERE email = :email OR name = :name");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':name', $name);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return false; // Email or name already exists
    }

    // Validate logo image for companies
    if (empty($logoImg)) {
        return false; // Company must provide a logo
    }

    // Insert company details
    $sql = "INSERT INTO company (name, email, password, tel,account_number, bio, address, logo_img) 
            VALUES (:name, :email, :password, :tel, :account_number, :bio, :address, :logo_img)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':account_number', $account_number);
    $stmt->bindParam(':bio', $bio);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':logo_img', $logoImg);

    $stmt->execute();

    return true;
}


