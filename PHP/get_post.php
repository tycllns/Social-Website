<?php
session_start();

// Initialize user ID
$specificUserId = 0;

// Check if the user_id is provided in the URL parameter
if (isset($_GET["user_id"]) && is_numeric($_GET["user_id"])) {
    $specificUserId = $_GET["user_id"];
} elseif (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    // Check if the user ID is set in the session
    if (isset($_SESSION["user_id"]) && is_numeric($_SESSION["user_id"])) {
        $specificUserId = $_SESSION["user_id"];
    } else {
        echo "Invalid user ID.";
        exit;
    }
} else {
    // Handle the case where neither a specific user ID nor a logged-in user ID is available
    echo "Invalid user ID.";
    exit;
}

// Create a connection to the database
include 'db_connect.php';

// SQL query to select specific columns from the 'posts' table and join with 'users' table
$sql = "SELECT DISTINCT p.post_id, u.username, u.profile_picture, p.content, p.created_at, p.user_id
        FROM posts AS p
        INNER JOIN users AS u ON p.user_id = u.user_id 
        LEFT JOIN comments AS c ON p.post_id = c.post_id
        WHERE p.user_id = $specificUserId OR c.user_id = $specificUserId
        ORDER BY p.created_at DESC";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<div class='post'>";
        echo "<img src='" . $row["profile_picture"] . "' class='profile_pic_small'>";
        echo "<div class='post_title'>" . $row["username"] . "</div><br>";
        echo "<div class='post_content'>" . $row["content"] . "</div><br>";
        echo "<div class='post_timestamp'>" . $row["created_at"] . "</div>";
        
        // Check if the logged-in user's ID matches the post's user_id
        if ($specificUserId == $row["user_id"]) {
            // Display the "Delete Post" button for posts belonging to the logged-in user
            echo "<form method='POST' action='PHP/delete_post.php'><input type='hidden' name='post_id' value='".$row["post_id"]."'><input type='submit' value='Delete Post'></form>";
        }

        // Retrieve comments for this post
        $post_id = $row["post_id"];
        $commentsSql = "SELECT c.comment_id, u.username, u.profile_picture, c.comment_content, c.created_at, c.user_id
                        FROM comments AS c
                        INNER JOIN users AS u ON c.user_id = u.user_id 
                        WHERE c.post_id = $post_id
                        ORDER BY c.created_at";
        $commentsResult = $conn->query($commentsSql);

        if ($commentsResult->num_rows > 0) {
            echo "<p>Comments</p>";
            echo "<div class='content'>";
            while ($commentRow = $commentsResult->fetch_assoc()) {
                echo "<div class='comment_box'><img src='" . $commentRow["profile_picture"] . "' class='profile_pic_small'><p class='post_title'>" . $commentRow["username"] . "</p>";
                echo "<p class='comment_content'>" . $commentRow["comment_content"] . "</p>";
                echo "<p class='post_timestamp'>" . $commentRow["created_at"] . "</p>";
                
                // Check if the logged-in user's ID matches the comment's user_id
                if ($specificUserId == $commentRow["user_id"]) {
                    // Display the "Delete Comment" button for comments belonging to the logged-in user
                    echo "<form method='POST' action='PHP/delete_comment.php'><input type='hidden' name='comment_id' value='".$commentRow["comment_id"]."'><input type='submit' value='Delete Comment'></form>";
                }
                
                echo "</div><br>";
            }
            echo "</div>";
        } else {
            echo "No comments for this post.";
        }

        // Comment form
        echo "<form method='POST' action='PHP/add_comment.php'>";
        echo "<input type='hidden' name='post_id' value='$post_id'>";
        echo "<input type='text' name='comment_content' placeholder='Add a comment'>";
        echo "<input type='submit' value='Submit Comment'>";
        echo "</form>";

        echo "</div>";
    }
} else {
    echo "No posts found for the specified user in the database.";
}

// Close the database connection
include 'db_disconnect.php';
?>
