<?php
require '../../connection.php';
$email = $_POST['email'];
$password = $_POST['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = "Format email tidak valid.";
    header('Location: ' . $host . '/authentication/lab-1/');
    exit;
}

// Siapkan statement untuk cegah SQL Injection
$stmt = $conn->prepare("SELECT username, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['username'] = $user['username'];
    header('Location: ' . $host . '/authentication/lab-1/profile.php');
    exit;
}

$_SESSION['error_message'] = "Email dan Password tidak cocok";
header('location: '.$host.'/authentication/lab-1/');
exit;
?>