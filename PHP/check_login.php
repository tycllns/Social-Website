<?php
session_start();

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    // User is not logged in, so send a JSON response indicating this
    $response = array("logged_in" => false);
    header("Content-Type: application/json");
    echo json_encode($response);
    exit;
}


$response = array("logged_in" => true);

header("Content-Type: application/json");
echo json_encode($response);
?>
