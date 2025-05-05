<?php
include "../../connection.php";

$getSaldo = $_POST['saldo'] ?? '';


if (!preg_match('/^Rp\s?([\d,\.]+)$/', $getSaldo, $matches)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Format saldo tidak valid',
    ]);
    exit;
}

$saldo = (int) filter_var($matches[1], FILTER_SANITIZE_NUMBER_INT);

if ($saldo <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Saldo tidak boleh nol atau negatif',
    ]);
    exit;
}

$cookieValue = $_COOKIE['accessLogin'] ?? null;

if (!$cookieValue || !preg_match('/^[a-f0-9]{32}$/', $cookieValue)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid session token',
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT s.saldo, s.user_id
    FROM access_login al 
    JOIN saldo s ON al.user_id = s.user_id
    WHERE al.token = ?
");
$stmt->bind_param("s", $cookieValue);
$stmt->execute();
$result = $stmt->get_result();
$userSaldo = $result->fetch_assoc();

if (!$userSaldo) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User tidak ditemukan',
    ]);
    exit;
}

if ($userSaldo['saldo'] < $saldo) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Saldo tidak mencukupi',
    ]);
    exit;
}
$finalSaldo = $userSaldo['saldo'] - $saldo;
$userID = $userSaldo['user_id'];

// Update saldo
$stmtUpdate = $conn->prepare("UPDATE saldo SET saldo = ? WHERE user_id = ?");
$stmtUpdate->bind_param("ii", $finalSaldo, $userID);
$stmtUpdate->execute();

// Insert history
$stmtHistory = $conn->prepare("INSERT INTO saldo_histories (user_id, saldo, status) VALUES (?, ?, 1)");
$stmtHistory->bind_param("ii", $userID, $saldo);
$stmtHistory->execute();

echo json_encode([
    'status' => 'success',
    'message' => 'Withdrawal successful',
]);
exit;

?>