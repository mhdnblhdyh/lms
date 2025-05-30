<?php
include 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validasi sederhana
    if ($name && $email && $password && $role) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

        if ($stmt->execute()) {
            $message = "✅ Akun berhasil dibuat! Silakan login.";
        } else {
            $message = "❌ Terjadi kesalahan saat mendaftar.";
        }
    } else {
        $message = "❗ Semua field wajib diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Mini LMS</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-container">
    <h2>📝 Daftar Akun</h2>
    <?php if ($message): ?>
        <p style="color: <?= str_contains($message, 'berhasil') ? 'green' : 'red' ?>;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="name" placeholder="Nama Lengkap" required>
        <input type="text" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Kata Sandi" required>
        <select name="role" required>
            <option value="">-- Pilih Role --</option>
            <option value="guru">👨‍🏫 Guru</option>
            <option value="siswa">👨‍🎓 Siswa</option>
        </select>
        <input type="submit" value="Daftar">
    </form>
    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
</div>
<button class="theme-toggle" onclick="toggleTheme()">🌓 Ganti Tema</button>

<script>
    function toggleTheme() {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
    }

    // Load preference
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
    }
</script>

</body>
</html>
