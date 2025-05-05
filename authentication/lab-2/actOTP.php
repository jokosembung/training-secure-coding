<?php
include "../../connection.php";

$phoneNumber = $_POST['phoneNumber'] ?? "";
$otp = $_POST['otp'] ?? "";


if (!preg_match('/^[0-9]{4,10}$/', $otp) || !preg_match('/^[0-9]{10,15}$/', $phoneNumber)) {
    $_SESSION['error_message'] = "Format OTP atau Nomor HP tidak valid";
    header('Location: ' . $host . '/authentication/lab-2/otp.php');
    exit;
}


//validate otp
$currentDate = date("Y-m-d H:i:s");
$query = "
    SELECT users.username FROM otp
    JOIN users ON users.id = otp.user_id
    WHERE otp.otp = ? AND users.phone_number = ? AND otp.expires_at <= ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $otp, $phoneNumber, $currentDate);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $_SESSION['username'] = $row['username'];
    header('Location: ' . $host . '/authentication/lab-2/profile.php');
    exit;
} else {
    $_SESSION['error_message'] = "Kode OTP Tidak Sesuai";
    header('Location: ' . $host . '/authentication/lab-2/otp.php');
    exit;
}