<?php
session_start();
include 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        if ($user['role'] === 'guru') {
            header("Location: teacher/dashboard.php");
        } else {
            header("Location: student/dashboard.php");
        }
        exit;
    } else {
        $message = "Email, password, atau role salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Mini LMS</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-container">
    <h2>🔐 Login Mini LMS</h2>
    <?php if ($message): ?>
        <p style="color: red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Kata Sandi" required>
        <select name="role" required>
            <option value="">-- Pilih Role --</option>
            <option value="guru">👨‍🏫 Guru</option>
            <option value="siswa">👨‍🎓 Siswa</option>
        </select>
        <input type="submit" value="Login">
    </form>
   <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
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
