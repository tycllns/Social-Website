<?php
session_start();

// Check if the session has expired
$session_expired = false;
if (!isset($_SESSION["logged_in"])) {
    $session_expired = true;
}

// Return the session status as JSON
echo json_encode(array("session_expired" => $session_expired));
?>
