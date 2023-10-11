<?php
// Start the session to work with session variables
session_start();

// Create a connection to the database
include 'db_connect.php';

// Get the user's username from the session
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];

    // SQL query to retrieve the user's ID based on their username
    $sql = "SELECT user_id FROM users WHERE username = ?";

    // Use prepared statements
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // User found, get their ID
        $row = $result->fetch_assoc();
        $user_id = $row["user_id"];

        // Variables for post data
        $content = $_POST['content'];
        $created_at = date("Y-m-d H:i:s"); // Current date and time

        // SQL query with prepared statement to insert data into the 'posts' table
        $sql = "INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $content, $created_at);

        // Execute the query and check for success
        if ($stmt->execute()) {
            $message = "Post added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
    } else {
        $message = "User not found in the database.";
    }
} else {
    $message = "User is not logged in.";
}

// Close the database connection
include 'db_disconnect.php';

// Output the message onto the webpage
echo $message;
?>
