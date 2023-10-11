<?php

// Ensure the $conn variable is defined (from your database connection script)
if (isset($conn) && $conn instanceof mysqli) {
    // Close the database connection
    $conn->close();
}

?>
