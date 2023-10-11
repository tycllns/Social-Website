<?php
// Start the session to work with session variables
session_start();

// Check if the session variables exist
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    // The user is logged in
    $user_id = $_SESSION["user_id"];
}
// Store the previous page URL
$_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];



// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the post_id and comment_content from the form
    $post_id = $_POST["post_id"];
    $comment_content = $_POST["comment_content"];

    // Create a connection to the database
    include 'db_connect.php';

    // SQL query to insert the comment into the 'comments' table
    $sql = "INSERT INTO comments (post_id, user_id, comment_content, created_at) VALUES (?, ?, ?, NOW())";

    // Prepare and execute the query using prepared statements
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iis", $post_id, $user_id, $comment_content);
        if ($stmt->execute()) {
            // Comment added successfully
        // Comment added successfully
        header("Location: " . $_SESSION['previous_page']);
        exit();

        } else {
            // Error adding comment
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    // Close the database connection
    include 'db_disconnect.php';
}
?>