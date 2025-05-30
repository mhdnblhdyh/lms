<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
echo "Selamat datang, " . htmlspecialchars($user['name']) . " (" . $user['role'] . ")!<br>";

if ($user['role'] === 'guru') {
    echo '<a href="teacher/create_class.php">+ Buat Kelas</a>';
} else {
    echo '<a href="student/join_class.php">+ Join Kelas</a>';
}
?>
<br><a href="logout.php">Logout</a>
