<?php
require '../../connection.php';
$phoneNumber = $_POST['phone_number'] ?? "";

if (!preg_match('/^[0-9]{10,15}$/', $phoneNumber)) {
    $_SESSION['error_message'] = "Format nomor handphone tidak valid";
    header('Location: ' . $host . '/authentication/lab-2/');
    exit;
}


$stmt = $conn->prepare("SELECT id, phone_number FROM users WHERE phone_number = ?");
$stmt->bind_param("s", $phoneNumber);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $_SESSION['phone_number'] = $row['phone_number'];
    $_SESSION['user_id'] = $row['id'];
    header('Location: ' . $host . '/authentication/lab-2/otp.php');
    exit;
} else {
    $_SESSION['error_message'] = "No Handphone tidak ditemukan";
    header('Location: ' . $host . '/authentication/lab-2/');
    exit;
}