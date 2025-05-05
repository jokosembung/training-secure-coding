<?php
require '../../vendor/autoload.php'; // Autoload Composer

// Koneksi ke MongoDB
$client = new MongoDB\Client("mongodb://localhost:27017");
$database = $client->selectDatabase('secure_coding');
$collection = $database->selectCollection('users');

// Ambil data JSON dari input
$data = json_decode(file_get_contents("php://input"), true);

// Ambil email dan password dari data yang di-decode
$email = $data['email'] ?? null; // Menggunakan null coalescing operator
$password = $data['password'] ?? null;

if ($email && $password) {
    // Query rentan terhadap NoSQL injection, query dibangun langsung dengan input pengguna
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["message" => "Email tidak valid!"]);
        exit;
    }

    $user = $collection->findOne(['email' => $email]);

    // Memeriksa apakah pengguna ditemukan
    if ($user) {
        // Verifikasi password dengan hash yang tersimpan
        if (password_verify($password, $user['password'])) {
            echo json_encode(["message" => "Login berhasil!"]);
        } else {
            echo json_encode(["message" => "Password tidak ditemukan!"]);
        }
    } else {
        echo json_encode(["message" => "Email tidak ditemukan!"]);
    }
} else {
    echo json_encode(["message" => "Email dan password tidak boleh kosong!"]);
}
?>
