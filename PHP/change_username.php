<?php

// Create a connection to the database
include 'db_connect.php';

// Start the session to work with session variables
session_start();

// Check if the user is logged in
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    // Get the new username from the form
    $newUsername = $_POST["new_username"];
    $userId = $_SESSION["user_id"];

    // SQL query to update the username
    $sql = "UPDATE users SET username = '$newUsername' WHERE user_id = $userId";

    // Execute the query and check for success
    if ($conn->query($sql) === TRUE) {
        $_SESSION["username"] = $newUsername; // Update the session variable
        $message = "Username updated successfully!";
    } else {
        $message = "Error updating username: " . $conn->error;
    }
} else {
    $message = "User is not logged in.";
}

// Close the database connection
include 'db_disconnect.php';

// Output the message
echo $message;
?>
