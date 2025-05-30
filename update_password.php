<?php
session_start();

require 'includes/db.php';
require 'includes/auth.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$userId = $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];

    // Ambil password lama dari DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch();

    if ($userData && password_verify($oldPassword, $userData['password'])) {
        // Update password baru
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hashedNewPassword, $userId]);

        $_SESSION['password_status'] = [
            'success' => true,
            'message' => 'Password berhasil diperbarui.'
        ];
    } else {
        $_SESSION['password_status'] = [
            'success' => false,
            'message' => 'Password lama salah.'
        ];
    }
}

header("Location: profile.php");
exit;
