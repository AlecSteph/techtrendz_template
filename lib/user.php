<?php

function addUser(PDO $pdo, string $first_name, string $last_name, string $email, string $password, $role = "user")
{
    $sql = "INSERT INTO users (first_name, last_name, email, password, role) VALUES (:first_name, :last_name, :email, :password, :role)";
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $query = $pdo->prepare($sql);
    $query->bindParam(':first_name', $first_name, PDO::PARAM_STR);
    $query->bindParam(':last_name', $last_name, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $query->bindParam(':role', $role, PDO::PARAM_STR);
    
    return $query->execute();
}

function verifyUserLoginPassword(PDO $pdo, string $email, string $password)
{
    $sql = "SELECT * FROM users WHERE email = :email";
    $query = $pdo->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    
    return false;
}
