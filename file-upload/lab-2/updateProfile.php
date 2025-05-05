<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ' . $host . '/file-upload/lab-1/');
    die();
}
include '../../connection.php';


$cookieValue = $_COOKIE['accessLogin'] ?? null;

if (!$cookieValue || !preg_match('/^[a-f0-9]{32}$/', $cookieValue)) {
    $message = "Token tidak valid";
    header("Location: $host/file-upload/lab-2/index.php?message=" . urlencode($message));
    exit;
}


$stmt = $conn->prepare("
    SELECT u.username, u.id, p.avatar 
    FROM users u
    JOIN access_login al ON al.user_id = u.id
    JOIN profiles p ON p.user_id = u.id
    WHERE al.token = ?
");
$stmt->bind_param("s", $cookieValue);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $message = "Data user tidak ditemukan";
    header("Location: $host/file-upload/lab-2/index.php?message=" . urlencode($message));
    exit;
}

$profile = $result->fetch_assoc();
$idUser = $profile['id'];
$currentAvatar = $profile['avatar'];

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
$directory = '../../assets/gallery/';
$fileName = $_FILES['avatar']['name'] ?? '';

if ($fileName && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['avatar']['tmp_name'];
    $mimeType = mime_content_type($fileTmp);
    
    if (!in_array($mimeType, $allowedTypes)) {
        $message = "Tipe file tidak diizinkan.";
        header("Location: $host/file-upload/lab-2/index.php?message=" . urlencode($message));
        exit;
    }

    // Generate nama file unik
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    $safeFileName = uniqid('avatar_', true) . '.' . $ext;
    $destination = $directory . $safeFileName;

    if (move_uploaded_file($fileTmp, $destination)) {
        // Hapus avatar lama jika ada
        if (!empty($currentAvatar) && file_exists($directory . $currentAvatar)) {
            unlink($directory . $currentAvatar);
        }
    } else {
        $message = "Gagal mengupload file.";
        header("Location: $host/file-upload/lab-2/index.php?message=" . urlencode($message));
        exit;
    }

    // Update avatar di database
    $stmtUpdate = $conn->prepare("UPDATE profiles SET avatar = ? WHERE user_id = ?");
    $stmtUpdate->bind_param("si", $safeFileName, $idUser);
    if (!$stmtUpdate->execute()) {
        $message = "Gagal mengupdate data profil.";
        header("Location: $host/file-upload/lab-2/index.php?message=" . urlencode($message));
        exit;
    }

    $message = "Profil berhasil diupdate.";
    header("Location: $host/file-upload/lab-2/index.php?message=" . urlencode($message));
    exit;
}

$message = "Tidak ada file yang dipilih.";
header("Location: $host/file-upload/lab-2/index.php?message=" . urlencode($message));
exit;