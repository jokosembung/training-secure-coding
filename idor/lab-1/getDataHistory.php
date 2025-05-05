<?php
include "../../connection.php";
$cookieValue = $_COOKIE['accessLogin'] ?? null;

if (!$cookieValue || !preg_match('/^[a-f0-9]{32}$/', $cookieValue)) {
    die("Token tidak valid.");
}
$stmt = $conn->prepare("
    SELECT s.*
    FROM access_login al 
    JOIN saldo_histories s ON al.user_id = s.user_id
    WHERE al.token = ?
");
$stmt->bind_param("s", $cookieValue);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query gagal dijalankan.");
}