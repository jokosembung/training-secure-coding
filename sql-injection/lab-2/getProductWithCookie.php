<?php
include '../../connection.php';

// Nama cookie
$accessLogin = 'accessLogin';
$isLogin = false;
$cookieValue = null;

if (isset($_COOKIE[$accessLogin])) {
    $cookieValue = $_COOKIE[$accessLogin];
} else {
    // Jika cookie tidak ada, buat token baru dan set cookie dengan waktu 30 hari
    $accessLogin = bin2hex(random_bytes(16));
    setcookie('accessLogin', $accessLogin, time() + (86400 * 30), "/", "", true, true); // Set Secure dan HttpOnly flags
}

$queryToken = "SELECT u.username 
               FROM users u
               JOIN access_login al ON al.user_id = u.id
               WHERE al.token = ?";
$stmt = $conn->prepare($queryToken);
$stmt->bind_param("s", $cookieValue); // Bind token ke query
$stmt->execute();
$resultQueryToken = $stmt->get_result();

if ($resultQueryToken->num_rows > 0) {
    $accessLogin = $cookieValue;
    $isLogin = true;
    $username = $resultQueryToken->fetch_assoc()['username'];
}

$category = @$_GET['category'];

if ($category == null) {
    $sql = "SELECT p.id, p.name, p.price, p.thumbnail, pc.category_name
            FROM products p
            JOIN product_categories pc ON pc.id = p.product_category_id
            WHERE p.is_publish = true";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
} else {
    $category = htmlspecialchars($category, ENT_QUOTES, 'UTF-8');

    $sql = "SELECT p.id, p.name, p.price, p.thumbnail, pc.category_name
            FROM products p
            JOIN product_categories pc ON pc.id = p.product_category_id
            WHERE pc.category_name = ? AND p.is_publish = true";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category); // Bind parameter kategori yang sudah disanitasi
    $stmt->execute();
}

//var_dump($sql);
$result = $stmt->get_result();
