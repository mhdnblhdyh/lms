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
    <title>Materi Kelas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<div class="wrapper">
    <?php include '../includes/sidebar_siswa.php'; ?>

    <div class="main">
        <div class="header">
            <h1>📘 Materi Kelas</h1>
            <div class="welcome">
                Selamat datang, <?= htmlspecialchars($_SESSION['user']['name']) ?> |
                <a href="../logout.php">Logout</a>
            </div>
        </div>

        <?php
        $user_id = $_SESSION['user']['id'];

        // Ambil semua kelas yang sudah diikuti oleh siswa
        $class_query = $conn->prepare("
            SELECT c.id, c.name 
            FROM classes c
            JOIN class_members cm ON cm.class_id = c.id
            WHERE cm.student_id = ?
        ");
        $class_query->bind_param("i", $user_id);
        $class_query->execute();
        $result = $class_query->get_result();

        $classes = [];
        while ($row = $result->fetch_assoc()) {
            $classes[] = $row;
        }
        ?>

        <?php foreach ($classes as $class): ?>
            <div class="card">
                <h2><?= htmlspecialchars($class['name']) ?></h2>
                <ul>
                    <?php
                    $material_query = $conn->prepare("SELECT * FROM materials WHERE class_id = ?");
                    $material_query->bind_param("i", $class['id']);
                    $material_query->execute();
                    $material_result = $material_query->get_result();

                    if ($material_result->num_rows === 0) {
                        echo "<li><em>Belum ada materi.</em></li>";
                    }

                    while ($material = $material_result->fetch_assoc()):
                    ?>
                        <li>
                            <?= htmlspecialchars($material['title']) ?> -
                            <?php if (!empty($material['file_path'])): ?>
                                <a href="../uploads/<?= htmlspecialchars($material['file_path']) ?>" target="_blank">📄 Lihat PDF</a>
                            <?php elseif (!empty($material['link'])): ?>
                                <a href="<?= htmlspecialchars($material['link']) ?>" target="_blank">🔗 Kunjungi Link</a>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
