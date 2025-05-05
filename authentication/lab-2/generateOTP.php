<?php
require "../../connection.php";
if(!$_SESSION['phone_number'] || !$_SESSION['user_id']){
    header('Location: '.$host.'/authentication/lab-2');
    exit();
}

$phoneNumber = $_SESSION['phone_number'];
$userID = $_SESSION['user_id'];
$otp = rand(1000, 9999);
$createdAt = date("Y-m-d H:i:s");
$expiresAt = date("Y-m-d H:i:s", strtotime("+5 minutes"));

$deleteStmt = $conn->prepare("DELETE FROM otp WHERE user_id = ?");
$deleteStmt->bind_param("i", $userID);
$deleteStmt->execute();

$insertStmt = $conn->prepare("INSERT INTO otp (otp, user_id, created_at, expires_at) VALUES (?, ?, ?, ?)");
$insertStmt->bind_param("siss", $otp, $userID, $createdAt, $expiresAt);

if ($insertStmt->execute()) {
    echo "OTP kamu adalah: $otp";
} else {
    $_SESSION['error_message'] = "Gagal menghasilkan OTP. Silakan coba lagi.";
    header('Location: ' . $host . '/authentication/lab-2');
    exit();
}


?>