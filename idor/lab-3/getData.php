<?php
include "../../connection.php";
$userID = $_GET['id'] ?? null;

// Pastikan ID ada dan valid
if (!$userID || !is_numeric($userID)) {
    die("Invalid user ID");
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userID); // 'i' untuk integer
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("User tidak ditemukan");
}

// Akses data user dari $row
$username = $row['username'];
$email = $row['email'];
