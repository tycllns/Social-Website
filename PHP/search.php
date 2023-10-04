<?php
// Check if the session variables exist
session_start();

// Create a connection to the database
include 'db_connect.php';

// Initialize username variable
$specificUsername = "";

// Check if the username is provided in the URL parameter
if (isset($_GET["username"])) {
    $specificUsername = $_GET["username"];
} else {
    // Handle the case where the username is not provided
    echo json_encode(array("success" => false, "message" => "Username not provided."));
    exit;
}

// SQL query to search for users by username
$sql = "SELECT * FROM users WHERE username LIKE ?";

// Execute the query with the specific username
if ($stmt = $conn->prepare($sql)) {
    // Add '%' wildcard to search for usernames containing the provided text
    $searchParam = "%" . $specificUsername . "%";
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Initialize an array to store user data
        $usersData = array();

        while ($row = $result->fetch_assoc()) {
            // Add user data to the array
            $usersData[] = array(
                "user_id" => $row["user_id"],
                "username" => $row["username"],
                "profile_picture" => $row["profile_picture"],
                // Add more user data as needed
            );
        }

        // Return the users data as JSON
        echo json_encode(array("success" => true, "users" => $usersData));
    } else {
        // No users found for the specified username
        echo json_encode(array("success" => true, "message" => "No users found for the specified username."));
    }

    $stmt->close();
} else {
    // Error in the SQL query
    echo json_encode(array("success" => false, "message" => "Error: " . $conn->error));
}

// Close the database connection
include 'db_disconnect.php';
?>
