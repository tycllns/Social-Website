<?php
// Start the session to work with session variables
session_start();


// Create a connection to the database
include 'db_connect.php';


// Get the username and password from the form
$username = $_POST["username"];
$password = $_POST["password"];

// SQL query to retrieve the hashed password and user_id for the given username
$sql = "SELECT user_id, password FROM users WHERE username = '$username'";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    // User found, check the password
    $row = $result->fetch_assoc();
    $hashedPassword = $row["password"];
    $user_id = $row["user_id"];
    
    if (password_verify($password, $hashedPassword)) {
        // Password is correct
        $_SESSION["logged_in"] = true; // Set the logged_in variable in the session
        $_SESSION["username"] = $username; // Set the username in the session
        $_SESSION["user_id"] = $user_id; // Set the user_id in the session
        header("Location: ../index.html"); // Redirect to the dashboard or another page
    } else {
        // Password is incorrect
        header("Location: ../login.html?error=1"); // Redirect back to the login page with an error parameter
    }
} else {
    // User not found
    header("Location: ../login.html?error=2"); // Redirect back to the login page with an error parameter
}

// Close the database connection
include 'db_disconnect.php';
?>
