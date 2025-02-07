<?php

function connectDB() {
    $host = 'localhost';
    $dbname = 'rotator'; // Replace with your database name
    $username = 'root';  // Replace with your MySQL username
    $password = '';  // Replace with your MySQL password

    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

    // Create redirects table if not exists
    try {
        $db->exec("
            CREATE TABLE IF NOT EXISTS redirects (
                id INT AUTO_INCREMENT PRIMARY KEY,
                phone_number VARCHAR(20) NOT NULL,
                redirect_date DATE NOT NULL,
                count INT NOT NULL
            )
        ");
    } catch (PDOException $e) {
        die("Table creation failed: " . $e->getMessage());
    }

    // Create WA table if not exists, and drop the old whatsapp_numbers table
    try {
        $db->exec("DROP TABLE IF EXISTS whatsapp_numbers");
        $db->exec("
            CREATE TABLE IF NOT EXISTS WA (
                id INT AUTO_INCREMENT PRIMARY KEY,
                whatsappnumber VARCHAR(20) NOT NULL UNIQUE,
                name VARCHAR(255) NOT NULL
            )
        ");
    } catch (PDOException $e) {
        die("Table creation failed: " . $e->getMessage());
    }

    return $db;
}
