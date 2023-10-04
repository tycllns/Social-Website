<?php

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "3Rwcv569";
$dbname = "social_media_app";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// You can include this script in your other PHP files to establish a database connection.
// Example usage in another PHP file:
// include 'db_connection.php';
// Now you have the $conn variable available for database operations.

?>
