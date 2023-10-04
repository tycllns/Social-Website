<?php
// Start the session to work with session variables
session_start();

// Check if the user is logged in
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page after logout
    header("Location: ../login.html");
    exit();
} else {
    // If the user is not logged in, you can handle this case as needed
    header("Location: ../login.html"); // Redirect to the login page
    exit();
}
?>
