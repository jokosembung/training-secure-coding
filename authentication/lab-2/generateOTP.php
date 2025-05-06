<?php
require "../../connection.php";
if(!$_SESSION['phone_number'] || !$_SESSION['user_id']){
    header('Location: '.$host.'/authentication/lab-2');
    exit();
}

$ipAddr = $_SERVER['REMOTE_ADDR'];
$attempt= 5;
$waktu = 5 * 60; //dalam menit


$phoneNumber = $_SESSION['phone_number'];
$userID = $_SESSION['user_id'];
$otp = rand(1000, 9999);
$createdAt = date("Y-m-d H:i:s");
$expiresAt = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// inisialisasi
if (!isset($_SESSION['otp_'.$ipAddr.'_'.$userID])){
    $_SESSION['otp_'.$ipAddr.'_'.$userID] = 0;
    $_SESSION['lastotptime_'.$ipAddr.'_'.$userID] = time();
}

// validasi ratelimit
if ($_SESSION['otp_'.$ipAddr.'_'.$userID] >= $attempt){
    $timeawal = time() - $_SESSION['lastotptime_'.$ipAddr.'_'.$userID];
    if($timeawal < $waktu){
        $_SESSION['error_message'] = "Anda sudah melebihi limit request otp dalam ." . $waktu;
        header('Location: ' . $host . '/authentication/lab-1/');
        exit;
    }
    $_SESSION['otp_'.$ipAddr.'_'.$userID] = 0;
}

$deleteStmt = $conn->prepare("DELETE FROM otp WHERE user_id = ?");
$deleteStmt->bind_param("i", $userID);
$deleteStmt->execute();

$insertStmt = $conn->prepare("INSERT INTO otp (otp, user_id, created_at, expires_at) VALUES (?, ?, ?, ?)");
$insertStmt->bind_param("siss", $otp, $userID, $createdAt, $expiresAt);

if ($insertStmt->execute()) {
    echo "OTP kamu adalah: $otp";
    $_SESSION['otp_'.$ipAddr.'_'.$userID]++;
    $_SESSION['lastotptime_'.$ipAddr.'_'.$userID] = time();
} else {
    $_SESSION['error_message'] = "Gagal menghasilkan OTP. Silakan coba lagi.";
    header('Location: ' . $host . '/authentication/lab-2');
    exit();
}


?>