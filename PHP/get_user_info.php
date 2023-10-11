<?php
session_start();

$response = array();

if (isset($_SESSION["username"])) {
    $response["username"] = $_SESSION["username"];
    include 'db_connect.php';

    $username = $_SESSION["username"];
    $userQuery = "SELECT user_id, profile_picture FROM users WHERE username = '$username'";
    $userResult = $conn->query($userQuery);

    if ($userResult->num_rows == 1) {
        $userRow = $userResult->fetch_assoc();
        $response["user_id"] = $userRow["user_id"];
        $response["profile_picture"] = $userRow["profile_picture"];
    } else {
        $response["user_id"] = "Unknown";
    }

    include 'db_disconnect.php';
} else {
    $response["username"] = "Unknown";
}

header("Content-Type: application/json");
echo json_encode($response);
?>
