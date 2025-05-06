<?php
require '../../connection.php';
$email = $_POST['email'];
$password = $_POST['password'];

$ipAddr = $_SERVER['REMOTE_ADDR'];
$attempt= 5;
$waktu = 1 * 60; //dalam menit

// inisialisasi
if (!isset($_SESSION['login_'.$ipAddr.'_'.$email])){
    $_SESSION['login_'.$ipAddr.'_'.$email] = 0;
    $_SESSION['lasttime_'.$ipAddr.'_'.$email] = time();
}

// validasi ratelimit
if ($_SESSION['login_'.$ipAddr.'_'.$email] >= $attempt){
    $timeawal = time() - $_SESSION['lasttime_'.$ipAddr.'_'.$email];
    if($timeawal < $waktu){
        $_SESSION['error_message'] = "Anda sudah melebihi limit percobaan gagal.";
        header('Location: ' . $host . '/authentication/lab-1/');
        exit;
    }
    $_SESSION['login_'.$ipAddr.'_'.$email] = 0;
}

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
    $_SESSION['login_'.$ipAddr.'_'.$email] = 0;
    $_SESSION['lasttime_'.$ipAddr.'_'.$email] = time();
    $_SESSION['username'] = $user['username'];
    header('Location: ' . $host . '/authentication/lab-1/profile.php');
    exit;
}



$_SESSION['error_message'] = "Email dan Password tidak cocok";
header('location: '.$host.'/authentication/lab-1/');
$_SESSION['login_'.$ipAddr.'_'.$email]++;
$_SESSION['lasttime_'.$ipAddr.'_'.$email] = time();
exit;
?>