<?php
// Start the session to work with session variables
session_start();

include 'db_connect.php';

// Create a response array
$response = array();

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input from the form
    $newUsername = $_POST["new_username"];
    $newPassword = $_POST["new_password"];
    $newEmail = $_POST["new_email"]; // Assuming you have an email input field

    // You should implement proper password hashing and validation here.
    // This is a simple example for demonstration purposes only.
    // Hash the password (using PHP's built-in password_hash function)
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Check if the username already exists
    $checkUsernameSql = "SELECT username FROM users WHERE username = ?";
    if ($checkUsernameStmt = $conn->prepare($checkUsernameSql)) {
        $checkUsernameStmt->bind_param("s", $newUsername);
        $checkUsernameStmt->execute();
        $checkUsernameStmt->store_result();

        if ($checkUsernameStmt->num_rows > 0) {
            // Username already exists, set an error message in the session
            $_SESSION['error_message'] = "Username already exists. Please choose a different username.";
            // Redirect to the create_user.html page
            header("Location: ../create_user.html?message=Username+already+exists");
            exit();
        } else {
            // Username is available, proceed with user creation
            $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sss", $newUsername, $hashedPassword, $newEmail);

                if ($stmt->execute()) {
                    // User created successfully
                    $user_id = $stmt->insert_id;

                    // Handle profile picture upload (if provided)
                    if (isset($_FILES["profile_picture"])) {
                        $file = $_FILES["profile_picture"];

                        // Check for file upload errors
                        if ($file["error"] === UPLOAD_ERR_OK) {
                            // Specify the directory where the profile pictures will be stored
                            $upload_dir = "../profile_pictures/";

                            // Generate a unique file name for the uploaded profile picture
                            $profile_picture_name = uniqid() . "_" . basename($file["name"]);
                            $target_path = $upload_dir . $profile_picture_name;

                            // Move the uploaded file to the target directory
                            if (move_uploaded_file($file["tmp_name"], $target_path)) {
                                // Update the user's profile picture path in the database
                                $profile_picture_path = "profile_pictures/" . $profile_picture_name;
                                $updateProfilePicSql = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
                                if ($updateStmt = $conn->prepare($updateProfilePicSql)) {
                                    $updateStmt->bind_param("si", $profile_picture_path, $user_id);
                                    $updateStmt->execute();
                                    $updateStmt->close();
                                }
                            } else {
                                // Error moving the uploaded file
                                $response['success'] = false;
                                $response['message'] = "Error uploading the profile picture.";
                            }
                        } else {
                            // Handle file upload errors
                            $response['success'] = false;
                            $response['message'] = "Error: File upload failed with error code " . $file["error"];
                        }
                    }

                    // User created and profile picture (if provided) handled successfully
                    // Redirect to login page
                    header("Location: ../login.html?message=User+Created+Successfully");
                    exit(); // Terminate script execution after redirection
                } else {
                    // Error creating user
                    $response['success'] = false;
                    $response['message'] = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $response['success'] = false;
                $response['message'] = "Error: " . $conn->error;
            }
        }

        $checkUsernameStmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = "Error: " . $conn->error;
    }
} else {
    // Invalid request method
    $response['success'] = false;
    $response['message'] = "Invalid request method.";
}

// Close the database connection
include 'db_disconnect.php';

// Display error message if there are any
if (!$response['success']) {
    echo $response['message'];
}
?>
