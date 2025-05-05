<?php
include "../../../connection.php";

$headers = getallheaders();
$authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';


if (preg_match('/Bearer (.+)/', $authorization, $matches)) {
    $token = $matches[1];
} else {
    echo json_encode(['result' => 0]);
    exit();
}

$stmt = $conn->prepare("
    SELECT users.id, users.role FROM access_login
    JOIN users ON users.id = access_login.user_id
    WHERE access_login.token = ?
");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['result' => 0]);
    exit();
}

$user = $result->fetch_assoc();

//$data = json_decode(file_get_contents('php://input'), true);
//validasi role by db bukan dari inputan
$user = $result->fetch_assoc();

if ($user['role'] === 'member') {
    echo json_encode(['result' => 1, 'message' => 'Welcome to the dashboard']);
} else {
    echo json_encode(['result' => 0]);
}
?>
