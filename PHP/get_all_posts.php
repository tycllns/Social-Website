<?php
session_start();

$specificUserId = 0;

if (isset($_GET["user_id"]) && is_numeric($_GET["user_id"])) {
    $specificUserId = $_GET["user_id"];
} elseif (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    if (isset($_SESSION["user_id"]) && is_numeric($_SESSION["user_id"])) {
        $specificUserId = $_SESSION["user_id"];
    } else {
        echo "Invalid user ID.";
        exit;
    }
} else {
    echo "Invalid user ID.";
    exit;
}

include 'db_connect.php';

$sql = "SELECT posts.post_id, users.user_id, users.username, users.profile_picture, posts.content, posts.created_at 
        FROM posts 
        INNER JOIN users ON posts.user_id = users.user_id
        ORDER BY posts.created_at DESC";

$result = $conn->query($sql);

$posts = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

foreach ($posts as $row) {
    echo "<div class='post'>";
    echo "<a href='other_profile.html?user_id=" . $row["user_id"] . "'><img src='" . $row["profile_picture"] . "' class='profile_pic_small'></a>";
    echo "<div class='post_title'>" . $row["username"] . "</div><br>";
    echo "<div class='post_content'>" . $row["content"] . "</div><br>";
    echo "<div class='post_timestamp'>" . $row["created_at"] . "</div><br>";

    $post_id = $row["post_id"];
    $commentsSql = "SELECT comment_id, users.username, users.profile_picture, comment_content, comments.created_at, comments.user_id 
                    FROM comments 
                    INNER JOIN users ON comments.user_id = users.user_id 
                    WHERE comments.post_id = $post_id";
    $commentsResult = $conn->query($commentsSql);

    if ($commentsResult->num_rows > 0) {
        echo "<div class='content'>";
        while ($commentRow = $commentsResult->fetch_assoc()) {
            echo "<div class='comment_box'><img src='" . $commentRow["profile_picture"] . "' class='profile_pic_small'><p class='post_title'> " . $commentRow["username"] . "</p>";
            echo "<p class='comment_content'> " . $commentRow["comment_content"] . "</p>";
            echo "<p class='post_timestamp'> " . $commentRow["created_at"] . "</p>";
            
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

    // Check if the logged-in user's ID matches the post's user_id
    if ($specificUserId == $row["user_id"]) {
        // Display the "Delete Post" button for posts belonging to the logged-in user
        echo "<form method='POST' action='PHP/delete_post.php'><input type='hidden' name='post_id' value='".$row["post_id"]."'><input type='submit' value='Delete Post'></form>";
    }

    echo "</div>";
}

include 'db_disconnect.php';
?>