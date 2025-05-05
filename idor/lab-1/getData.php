<?php
require '../../connection.php';

//cookie user id 3
setcookie('accessLogin', "01ed90ff6945d13d3bd60174e7bd057e", time() + (86400 * 30), "/"); // Cookie berlaku selama 30 hari

$cookieValue = $_COOKIE['accessLogin'] ?? null;

if (!$cookieValue || !preg_match('/^[a-f0-9]{32}$/', $cookieValue)) {
    die("Token tidak valid atau tidak ada.");
}
$stmt = $conn->prepare("
    SELECT s.saldo
    FROM access_login al
    JOIN saldo s ON al.user_id = s.user_id
    WHERE al.token = ?
");
$stmt->bind_param("s", $cookieValue);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Data saldo tidak ditemukan.");
}

$data = $result->fetch_assoc();