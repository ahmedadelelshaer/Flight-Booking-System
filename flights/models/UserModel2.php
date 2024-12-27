<?php

include_once '../php/includes/db.php';


function get_users($sortOrder = 'ASC')
{
    $conn = connectToDB();
    if ($conn) {
        $stmt = $conn->prepare("SELECT * FROM user ORDER BY id $sortOrder");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return [];
    }
}
function login($email, $password)
{
    $conn = connectToDB();
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email AND password = :password");
    $stmt->execute(['email' => $email, 'password' => $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user;
}



function register($name, $email, $password, $tel, $account, $type, $bio = '', $address = '', $photo = '', $passportImg = '', $logoImg = '')
{
    $conn = connectToDB();

    // Check for existing email or name
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email OR name = :name");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':name', $name);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return false; // Email or name already exists
    }

    // Insert basic user details
    $sql = "INSERT INTO user (name, email, password, tel, account, type, bio, address, photo, passport_img, logo_img) 
    VALUES (:name, :email, :password, :tel, :account, :type, :bio, :address, :photo, :passport_img, :logo_img)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':account', $account);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':bio', $bio);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':photo', $photo);
    $stmt->bindParam(':passport_img', $passportImg);
    $stmt->bindParam(':logo_img', $logoImg);

    $stmt->execute();

    return true;
}


function register_passenger($photo, $passportImg, $name)
{
    $conn = connectToDB();

    // Update the passenger record where the name matches the given name
    $sql = "UPDATE user SET photo = :photo, passport_img = :passport_img WHERE name= :name ";
    $stmt = $conn->prepare($sql);

    // Bind parameters

    $stmt->bindParam(':photo', $photo);
    $stmt->bindParam(':passport_img', $passportImg);
    $stmt->bindParam(':name', $name);

    // Execute the query
    $stmt->execute();

    return true;
}

function register_company($bio, $address, $logoImg = null, $name)
{
    $conn = connectToDB();

    // Insert company-specific data
    $sql = "INSERT INTO user ( bio, address, logo_img,) VALUES (:bio, :address, :logo_img) where name=$name";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':bio', $bio);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':logo_img', $logoImg);
    $stmt->bindParam('', $name);
    $stmt->execute();

    return true;
}