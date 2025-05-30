<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    header("Location: ../login.php");
    exit;
}

include '../includes/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bergabung Kelas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        .form-join {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 500px;
            margin-bottom: 40px;
        }

        .form-join input[type="text"] {
            padding: 14px 18px;
            border: 2px solid #ddd;
            border-radius: 12px;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        .form-join input[type="text"]:focus {
            border-color: #9c88ff;
            box-shadow: 0 0 0 4px rgba(156, 136, 255, 0.2);
        }

        .class-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 30px;
        }

        .class-item {
            padding: 18px 24px;
            background: #f5f4fb;
            border-left: 6px solid #9c88ff;
            border-radius: 12px;
            font-weight: 600;
            color: #4b4559;
            box-shadow: 0 4px 10px rgba(156, 136, 255, 0.1);
        }

        .alert {
            padding: 14px 20px;
            background-color: #dff0d8;
            color: #3c763d;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #842029;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include '../includes/sidebar_siswa.php'; ?>

    <div class="main">
        <div class="header">
            <h1>➕ Bergabung ke Kelas</h1>
            <div class="welcome">
                Hai, <?= htmlspecialchars($_SESSION['user']['name']) ?> |
                <a href="../logout.php">Logout</a>
            </div>
        </div>

        <?php
        $user_id = $_SESSION['user']['id'];
        $success = $error = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $kode = trim($_POST["kode_kelas"]);

            $cek_kelas = $conn->prepare("SELECT id, name FROM classes WHERE code = ?");
            $cek_kelas->bind_param("s", $kode);
            $cek_kelas->execute();
            $result = $cek_kelas->get_result();

            if ($kelas = $result->fetch_assoc()) {
                $kelas_id = $kelas['id'];

                $cek_anggota = $conn->prepare("SELECT * FROM class_members WHERE class_id = ? AND student_id = ?");
                $cek_anggota->bind_param("ii", $kelas_id, $user_id);
                $cek_anggota->execute();
                $is_member = $cek_anggota->get_result()->num_rows > 0;

                if (!$is_member) {
                    $gabung = $conn->prepare("INSERT INTO class_members (class_id, student_id) VALUES (?, ?)");
                    $gabung->bind_param("ii", $kelas_id, $user_id);
                    $gabung->execute();
                    $success = "Berhasil bergabung dengan kelas: " . htmlspecialchars($kelas['name']);
                } else {
                    $error = "Kamu sudah terdaftar di kelas ini.";
                }
            } else {
                $error = "Kode kelas tidak valid.";
            }
        }
        ?>

        <?php if ($success): ?>
            <div class="alert"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="form-join">
            <label for="kode_kelas"><strong>Masukkan Kode Kelas:</strong></label>
            <input type="text" name="kode_kelas" id="kode_kelas" required placeholder="Contoh: ABC123">
            <button type="submit">Gabung</button>
        </form>

        <h2 class="logo-title">📚 Kelas yang Sudah Kamu Ikuti</h2>

        <div class="class-list">
            <?php
            $kelas_saya = $conn->prepare("
                SELECT c.name 
                FROM classes c 
                JOIN class_members cm ON cm.class_id = c.id 
                WHERE cm.student_id = ?
            ");
            $kelas_saya->bind_param("i", $user_id);
            $kelas_saya->execute();
            $result = $kelas_saya->get_result();

            if ($result->num_rows === 0) {
                echo "<p><em>Kamu belum bergabung ke kelas manapun.</em></p>";
            } else {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='class-item'>📘 " . htmlspecialchars($row['name']) . "</div>";
                }
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>
