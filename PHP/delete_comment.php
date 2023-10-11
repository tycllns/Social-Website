<?php
session_start();

// Check if the session variables exist
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    echo "You must be logged in to delete a comment.";
    exit;
}

// Check if the specific user ID is available in the session
if (!isset($_SESSION["user_id"]) || !is_numeric($_SESSION["user_id"])) {
    echo "Invalid user ID.";
    exit;
}

// Check if the comment_id is provided via a POST request
if (isset($_POST["comment_id"]) && is_numeric($_POST["comment_id"])) {
    $commentToDelete = $_POST["comment_id"];
} else {
    echo "Invalid comment ID.";
    exit;
}

// Create a connection to the database
include 'db_connect.php';

// Query to check if the logged-in user has permission to delete this comment
$user_id = $_SESSION["user_id"];
$sql = "SELECT user_id FROM comments WHERE comment_id = $commentToDelete";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $commentUserId = $row["user_id"];

    if ($commentUserId == $user_id) {
        // User has permission to delete the comment
        $deleteSql = "DELETE FROM comments WHERE comment_id = $commentToDelete";
        if ($conn->query($deleteSql) === TRUE) {
            // Redirect back to the previous page
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        } else {
            echo "Error deleting comment: " . $conn->error;
        }
    } else {
        echo "You do not have permission to delete this comment.";
    }
} else {
    echo "Comment not found.";
}

// Close the database connection
include 'db_disconnect.php';
?>
