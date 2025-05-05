<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ' . $host . '/file-upload/lab-1/');
    die();
}
include '../../connection.php';


$cookieValue = $_COOKIE['accessLogin'] ?? '';
if (!$cookieValue) {
    header('Location: ' . $host . '/file-upload/lab-1/index.php?message=' . urlencode("Unauthorized"));
    exit();
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
$resultQueryToken = $stmt->get_result();

if ($resultQueryToken->num_rows === 0) {
    header('Location: ' . $host . '/file-upload/lab-1/index.php?message=' . urlencode("Data user tidak ditemukan"));
    exit();
}

$profile = $resultQueryToken->fetch_assoc();
$idUser = $profile['id'];
$currentAvatar = $profile['avatar'];


//update profile
$directory = '../../assets/gallery/';
$fileName = $_FILES['avatar']['name'] ?? '';
$tmpName = $_FILES['avatar']['tmp_name'] ?? '';
$avatar = $currentAvatar;



if ($fileName && $tmpName) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($tmpName);
    
    if (!in_array($fileType, $allowedTypes)) {
        $message = "File harus berupa gambar (jpeg/png/gif)";
        header('Location: ' . $host . '/file-upload/lab-1/index.php?message=' . urlencode($message));
        exit();
    }

    // Hindari duplikasi nama file, bisa pakai timestamp atau uniqid
    $safeFileName = uniqid('avatar_') . '_' . basename($fileName);
    $targetPath = $directory . $safeFileName;

    if (move_uploaded_file($tmpName, $targetPath)) {
        // Hapus avatar lama (jika ada dan bukan default)
        if ($currentAvatar && file_exists($directory . $currentAvatar)) {
            unlink($directory . $currentAvatar);
        }
        $avatar = $safeFileName;
    } else {
        $message = "File gagal diupload.";
        header('Location: ' . $host . '/file-upload/lab-1/index.php?message=' . urlencode($message));
        exit();
    }
}

$idUser = $profile['id'];

$avatar = $profile['avatar'];

$updateStmt = $conn->prepare("UPDATE profiles SET avatar = ? WHERE user_id = ?");
$updateStmt->bind_param("si", $avatar, $idUser);
if ($updateStmt->execute()) {
    $message = "Data profile berhasil diupdate";
} else {
    $message = "Gagal mengupdate profile";
}

header('Location: ' . $host . '/file-upload/lab-1/index.php?message=' . urlencode($message));
exit();