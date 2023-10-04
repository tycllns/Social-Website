<?php
// Check if the session variables exist
session_start();

// Create a connection to the database
include 'db_connect.php';

// Initialize user ID
$specificUserId = 0;

// Check if the user_id is provided in the URL parameter
if (isset($_GET["user_id"]) && is_numeric($_GET["user_id"])) {
    $specificUserId = $_GET["user_id"];
} elseif (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    // If no specific user ID is provided, use the logged-in user's ID
    $specificUserId = $_SESSION["user_id"];
} else {
    // Handle the case where neither a specific user ID nor a logged-in user ID is available
    echo json_encode(array("success" => false, "message" => "Invalid user ID."));
    exit;
}

// SQL query to select specific columns from the 'posts' table and join with 'users' table
$sql = "SELECT posts.post_id, users.username, users.profile_picture, posts.content, posts.created_at 
        FROM posts 
        INNER JOIN users ON posts.user_id = users.user_id 
        WHERE users.user_id = ?";

// Execute the query with the specific user's ID
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $specificUserId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Initialize an array to store post data
        $postsData = array();

        while ($row = $result->fetch_assoc()) {
            // Initialize an array for each post
            $postData = array(
                "profile_picture" => $row["profile_picture"],
                "username" => $row["username"],
                "content" => $row["content"],
                "created_at" => $row["created_at"],
                "comments" => array(),
            );

            // Retrieve comments for this post
            $post_id = $row["post_id"];
            $commentsSql = "SELECT comment_id, users.username, users.profile_picture, comment_content, comments.created_at 
                            FROM comments 
                            INNER JOIN users ON comments.user_id = users.user_id 
                            WHERE comments.post_id = ?";
            $commentsStmt = $conn->prepare($commentsSql);
            $commentsStmt->bind_param("i", $post_id);
            $commentsStmt->execute();
            $commentsResult = $commentsStmt->get_result();

            if ($commentsResult->num_rows > 0) {
                while ($commentRow = $commentsResult->fetch_assoc()) {
                    // Add each comment to the post's comments array
                    $postData["comments"][] = array(
                        "profile_picture" => $commentRow["profile_picture"],
                        "username" => $commentRow["username"],
                        "comment_content" => $commentRow["comment_content"],
                        "created_at" => $commentRow["created_at"],
                    );
                }
            }

            // Add the post data to the posts array
            $postsData[] = $postData;
            
        }

        // Return the posts data as JSON
        echo json_encode(array("success" => true, "posts" => $postsData));
    } else {
        // No posts found for the specified user
        echo json_encode(array("success" => true, "message" => "No posts found for the specified user."));
    }

    $stmt->close();
} else {
    // Error in the SQL query
    echo json_encode(array("success" => false, "message" => "Error: " . $conn->error));
}

// Close the database connection
include 'db_disconnect.php';
?>
