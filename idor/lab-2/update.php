<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ' . $host . '/idor/lab-2/');
    die();
}
include '../../connection.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$loggedInUserID = $_SESSION['user_id'];
$password = $_POST['password'] ?? '';

if (!$password) {
    $message = "Password tidak boleh kosong";
    header("Location: $host/idor/lab-2/index.php?message=" . urlencode($message));
    exit;
}

// Gunakan password_hash (bukan sha1)
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$userID = $_GET['id'];

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hashedPassword, $loggedInUserID);

if ($stmt->execute()) {
    $message = "Data profile berhasil diupdate";
} else {
    $message = "Data profile gagal diupdate";
}

header("Location: $host/idor/lab-2/index.php?message=" . urlencode($message));
exit;