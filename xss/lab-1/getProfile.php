<?php
include '../../connection.php';

//cookie user id 3
setcookie('accessLogin', "01ed90ff6945d13d3bd60174e7bd057e", time() + (86400 * 30), "/", "", true, true); // Secure and HttpOnly

$cookieValue = $_COOKIE['accessLogin'] ?? null;
$message = '';
if ($cookieValue) {
        // Use prepared statement to prevent SQL injection
        $sql = "SELECT p.* 
                FROM access_login al 
                JOIN profiles p ON al.user_id = p.user_id
                WHERE al.token = ?";

        // Prepare the query and bind the parameter securely
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cookieValue); // Bind token value
        $stmt->execute();

        // Fetch the result
        $result = $stmt->get_result();

        // Check if the profile exists
        if ($result->num_rows > 0) {
                $profile = $result->fetch_assoc();
                $fullname = $profile['fullname'] ?? null;
                $avatar = $profile['avatar'] ?? ""; // Default to empty string if avatar doesn't exist
        } else {
                $fullname = null;
                $avatar = null;
                $message = "Profile not found.";
        }
} else {
        $fullname = null;
        $avatar = null;
        $message = "User is not logged in.";
}

$message = $_GET['message'] ?? $message;