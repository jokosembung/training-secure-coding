<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ' . $host . '/xss/lab-1/');
    die();
}
include '../../connection.php';

$cookieValue = $_COOKIE['accessLogin'] ?? null;

if ($cookieValue) {
    // Use prepared statements for security
    $queryToken = "SELECT u.username, u.id, p.avatar 
                    FROM users u
                    JOIN access_login al ON al.user_id = u.id
                    JOIN profiles p ON p.user_id = u.id
                    WHERE al.token = ?";
    
    $stmt = $conn->prepare($queryToken);
    $stmt->bind_param("s", $cookieValue); // Bind token parameter
    $stmt->execute();
    $resultQueryToken = $stmt->get_result();

    // Check if the user is found
    if ($resultQueryToken->num_rows == 0) {
        $message = "Data user tidak ditemukan";
        header('Location: ' . $host . '/xss/case-1/index.php?message=' . urlencode($message));
        die();
    }

    // Fetch user profile data
    $profile = mysqli_fetch_assoc($resultQueryToken);

    // Sanitize and validate user input
    $fullname = $_POST['fullname'] ?? '';
    $fullname = trim($fullname);
    
    if (empty($fullname)) {
        $message = "Full name is required";
        header('Location: ' . $host . '/xss/case-1/index.php?message=' . urlencode($message));
        die();
    }

    // Prevent XSS by encoding the output
    $fullname = htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8');

    $idUser = $profile['id'];

    // Update profile using prepared statements
    $queryUpdateProfile = "UPDATE profiles 
                           SET fullname = ? 
                           WHERE user_id = ?";
    
    $stmtUpdate = $conn->prepare($queryUpdateProfile);
    $stmtUpdate->bind_param("si", $fullname, $idUser); // Bind parameters
    if ($stmtUpdate->execute() === FALSE) {
        $message = "Data profile gagal diupdate";
    } else {
        $message = "Data profile berhasil diupdate";
    }

    // Redirect with the result message
    header('Location: ' . $host . '/xss/lab-1/index.php?message=' . urlencode($message));
    die();

} else {
    // Handle if no cookie is found
    $message = "Access token not found";
    header('Location: ' . $host . '/xss/case-1/index.php?message=' . urlencode($message));
    die();
}