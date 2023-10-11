<?php
session_start();

$specificUserId = 0;

if (isset($_GET["user_id"]) && is_numeric($_GET["user_id"])) {
    $specificUserId = $_GET["user_id"];
} elseif (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    $specificUserId = $_SESSION["user_id"];
} else {
    echo "Invalid user ID.";
    exit;
}

include 'db_connect.php';

// Check if the post_id is provided via a URL parameter or form submission
if (isset($_GET["post_id"]) && is_numeric($_GET["post_id"])) {
    $postToDelete = $_GET["post_id"];
} elseif (isset($_POST["post_id"]) && is_numeric($_POST["post_id"])) {
    $postToDelete = $_POST["post_id"];
} else {
    echo "Invalid post ID.";
    exit;
}

// Check if the user has permission to delete this post
$sql = "SELECT user_id FROM posts WHERE post_id = $postToDelete";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $postUserId = $row["user_id"];

    if ($postUserId == $specificUserId) {
        // User has permission to delete the post
        // First, delete associated comments
        $deleteCommentsSql = "DELETE FROM comments WHERE post_id = $postToDelete";
        if ($conn->query($deleteCommentsSql) === TRUE) {
            // Comments deleted successfully, now delete the post
            $deletePostSql = "DELETE FROM posts WHERE post_id = $postToDelete";
            if ($conn->query($deletePostSql) === TRUE) {
                // Redirect back to the previous page
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            } else {
                echo "Error deleting post: " . $conn->error;
            }
        } else {
            echo "Error deleting comments: " . $conn->error;
        }
    } else {
        echo "You do not have permission to delete this post.";
    }
} else {
    echo "Post not found.";
}

include 'db_disconnect.php';
?>
