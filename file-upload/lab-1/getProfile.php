<?php
include '../../connection.php';


//cookie user id 3
setcookie('accessLogin', "63de90ff6945d13d3bd60174e7bd057e", time() + (86400 * 30), "/"); // Cookie berlaku selama 30 hari

$cookieValue = $_COOKIE['accessLogin'] ?? null;
$avatar = '';
$message = $_GET['message'] ?? '';

$avatar = '';
if (!$cookieValue || !preg_match('/^[a-f0-9]{32}$/', $cookieValue)) {
        $avatar = '';
} else {
        // Gunakan prepared statement
        $stmt = $conn->prepare("
                SELECT p.avatar
                FROM access_login al 
                JOIN profiles p ON al.user_id = p.user_id
                WHERE al.token = ?
                LIMIT 1
        ");
        $stmt->bind_param("s", $cookieValue);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
                $profile = $result->fetch_assoc();
                $avatar = $profile['avatar'] ?? 'default.png';
        } else {
                $avatar = 'default.png';
        }
}

$message = $_GET['message'] ?? "";
?>