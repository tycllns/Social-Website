<?php
$max_file_size = 30 * 1024 * 1024; // 30 MB
$allowed_file_types = array("image/jpeg", "image/png", "image/gif");

// Start the session to work with session variables
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create a response array
$response = array();

// Check if the session variables exist
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    // The user is logged in
    $user_id = $_SESSION["user_id"];

    // Check if the form has been submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if a file was uploaded
        if (isset($_FILES["profile_picture"])) {
            $file = $_FILES["profile_picture"];

            // Check for file upload errors
            if ($file["error"] === UPLOAD_ERR_OK) {
                // Check file size
                if ($file["size"] > $max_file_size) {
                    $response['success'] = false;
                    $response['message'] = "Error: File size exceeds the maximum allowed size.";
                } else {
                    $file_mime_type = mime_content_type($file["tmp_name"]);

                    if (!in_array($file_mime_type, $allowed_file_types)) {
                        // Invalid file type
                        $response['success'] = false;
                        $response['message'] = "Error: Invalid file type.";
                    } else {
                        // File upload is successful, proceed with file handling
                        // Specify the directory where the profile pictures will be stored
                        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/SocialWebsite/profile_pictures/";

                        // Generate a unique file name for the uploaded profile picture
                        $profile_picture_name = uniqid() . "_" . basename($file["name"]);
                        $target_path = $upload_dir . $profile_picture_name;

                        // Move the uploaded file to the target directory
                        if (move_uploaded_file($file["tmp_name"], $target_path)) {
                            // Update the user's profile picture path in the database
                            include 'db_connect.php';

                            // Check the connection
                            if ($conn->connect_error) {
                                $response['success'] = false;
                                $response['message'] = "Connection failed: " . $conn->connect_error;
                            } else {
                                // Set the profile picture path in the database
                                $profile_picture_path = "profile_pictures/" . $profile_picture_name;
                                $sql = "UPDATE users SET profile_picture = ? WHERE user_id = ?";

                                // Prepare and execute the query using prepared statements
                                if ($stmt = $conn->prepare($sql)) {
                                    $stmt->bind_param("si", $profile_picture_path, $user_id);
                                    if ($stmt->execute()) {
                                        // Profile picture updated successfully
                                        $response['success'] = true;
                                        $response['message'] = "Profile picture uploaded successfully.";

                                        // Redirect to upload.html after successful upload
                                        header("Location: ../upload.html?success=true");
                                        exit(); // Terminate script execution after redirection
                                    } else {
                                        // Error updating profile picture
                                        $response['success'] = false;
                                        $response['message'] = "Error: " . $stmt->error;
                                    }
                                    $stmt->close();
                                } else {
                                    $response['success'] = false;
                                    $response['message'] = "Error: " . $conn->error;
                                }

                                // Close the database connection
                                include 'db_disconnect.php';
                            }
                        } else {
                            // Error moving the uploaded file
                            $response['success'] = false;
                            $response['message'] = "Error uploading the profile picture.";
                        }
                    }
                }
            } else {
                // Handle file upload errors
                $response['success'] = false;
                $response['message'] = "Error: File upload failed with error code " . $file["error"];
            }
        } else {
            // No file uploaded
            $response['success'] = false;
            $response['message'] = "No file uploaded.";
        }
    } else {
        // Invalid request method
        $response['success'] = false;
        $response['message'] = "Invalid request method.";
    }
} else {
    // User not logged in
    $response['success'] = false;
    $response['message'] = "User not logged in. Please log in first.";
}

// Redirect to upload.html with an error message if there's an error
if (!$response['success']) {
    $error_message = urlencode($response['message']);
    header("Location: ../upload.html?error=$error_message");
    exit();
}

// Send the JSON response if there are no errors
header('Content-Type: application/json');
echo json_encode($response);
?>
