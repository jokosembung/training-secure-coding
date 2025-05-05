<?php
include '../../connection.php';

//cookie user id 3
setcookie('accessLogin', "01ed90ff6945d13d3bd60174e7bd057e", time() + (86400 * 30), "/"); // Cookie berlaku selama 30 hari

$cookieValue = $_COOKIE['accessLogin'] ?? null;
$avatar = '';
$userID = null;
$email = '';
$message = $_GET['message'] ?? "";

// Validasi token
if (!$cookieValue || !preg_match('/^[a-f0-9]{32}$/', $cookieValue)) {
    $message = "Session tidak valid";
    header("Location: $host/login.php?message=" . urlencode($message));
    exit;
}

// Gunakan prepared statement untuk keamanan
$stmt = $conn->prepare("
    SELECT u.* 
    FROM access_login al 
    JOIN users u ON al.user_id = u.id 
    WHERE al.token = ?
");
$stmt->bind_param("s", $cookieValue);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $message = "User tidak ditemukan atau session tidak sah";
    header("Location: $host/login.php?message=" . urlencode($message));
    exit;
}

$userID = $user['id'];
$email = $user['email'];