<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

include '../includes/db.php';
include '../includes/header.php';

$teacher_id = $_SESSION['user']['id'];
$name = $_SESSION['user']['name'];

// Ambil foto profil guru dari database
$user_photo_result = $conn->query("SELECT photo FROM users WHERE id = $teacher_id");
$user_photo_data = $user_photo_result->fetch_assoc();
$user_photo = $user_photo_data['photo'] ?? null;

// Statistik
$total_classes = $conn->query("SELECT COUNT(*) AS total FROM classes WHERE teacher_id = $teacher_id")->fetch_assoc()['total'];
$total_materials = $conn->query("
    SELECT COUNT(*) AS total FROM materials m
    JOIN classes c ON m.class_id = c.id
    WHERE c.teacher_id = $teacher_id
")->fetch_assoc()['total'];
$total_assignments = $conn->query("
    SELECT COUNT(*) AS total FROM assignments a
    JOIN classes c ON a.class_id = c.id
    WHERE c.teacher_id = $teacher_id
")->fetch_assoc()['total'];

// Tugas terbaru
$tugas_result = $conn->query("
    SELECT a.title, c.name AS class_name, a.deadline
    FROM assignments a
    JOIN classes c ON a.class_id = c.id
    WHERE c.teacher_id = $teacher_id
    ORDER BY a.created_at DESC
    LIMIT 3
");

// Tugas masuk terbaru
$submissions = $conn->query("
    SELECT s.id, u.name AS student_name, a.title AS assignment_title, s.submitted_at
    FROM submissions s
    JOIN assignments a ON s.assignment_id = a.id
    JOIN classes c ON a.class_id = c.id
    JOIN users u ON s.student_id = u.id
    WHERE c.teacher_id = $teacher_id
    ORDER BY s.submitted_at DESC
    LIMIT 5
");

// Pengumuman terbaru
$announcement = $conn->query("SELECT message FROM announcements ORDER BY created_at DESC LIMIT 1");
$latest_announcement = $announcement->num_rows ? $announcement->fetch_assoc()['message'] : null;
?>

<div class="wrapper">
    <?php include '../includes/sidebar_guru.php'; ?>

    <div class="main">
        <!-- Header -->
        <div class="top-bar" style="position: relative; margin-bottom: 30px;">
            
            <div class="logo-title">
                <h1 style="font-size: 32px; font-weight: 800; color: #6f5dca;">📚 Mini LMS</h1>
            </div>
             <h2>Dashboard Guru</h2>

            <!-- Foto Profil dan Logout di kanan atas -->
            <div style="position: absolute; top: -20px; right: 0; display: flex; flex-direction: column; align-items: center; gap: 6px;">
   <?php if (!empty($user_photo) && file_exists('../' . $user_photo)): ?>
    <a href="../profile.php" style="display: inline-block;">
        <img src="<?= '../' . htmlspecialchars($user_photo) ?>" alt="Foto Profil" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; box-shadow: 0 3px 8px rgba(0,0,0,0.1);">
    </a>
<?php else: ?>
    <a href="../profile.php" style="display: inline-block;">
        <div style="width: 48px; height: 48px; border-radius: 50%; background: #dcd6ff; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #6f5dca;">
            <?= strtoupper($name[0]) ?>
        </div>
    </a>
<?php endif; ?>

    <a href="../logout.php" style="
        padding: 4px 10px;
        background-color: #6f5dca;
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
    ">
        Logout
    </a>
</div>

        </div>
        <!-- Selamat Datang -->
        <div class="card">
            <p>Selamat datang, <strong><?= htmlspecialchars($name) ?></strong>!</p>
            <p>Silakan pilih menu di sidebar untuk memulai mengajar.</p>
        </div>

        <!-- Statistik -->
        <div class="card">
            <h3>📊 Statistik</h3>
            <ul>
                <li>Total Kelas: <strong><?= $total_classes ?></strong></li>
                <li>Total Materi: <strong><?= $total_materials ?></strong></li>
                <li>Total Tugas: <strong><?= $total_assignments ?></strong></li>
            </ul>
        </div>

        <!-- Tugas Terbaru -->
        <div class="card">
            <h3>📝 Tugas Terbaru</h3>
            <?php if ($tugas_result->num_rows): ?>
                <ul>
                    <?php while ($t = $tugas_result->fetch_assoc()): ?>
                        <li><strong><?= htmlspecialchars($t['title']) ?></strong> (<?= htmlspecialchars($t['class_name']) ?>) - Deadline: <?= $t['deadline'] ?></li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Belum ada tugas baru.</p>
            <?php endif; ?>
        </div>

        <!-- Tugas Masuk Terbaru -->
        <div class="card">
            <h3>📥 Tugas Masuk Terbaru</h3>
            <?php if ($submissions->num_rows): ?>
                <ul>
                    <?php while ($s = $submissions->fetch_assoc()): ?>
                        <li><?= htmlspecialchars($s['student_name']) ?> mengumpulkan <strong><?= htmlspecialchars($s['assignment_title']) ?></strong> pada <?= $s['submitted_at'] ?></li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Belum ada pengumpulan baru.</p>
            <?php endif; ?>
        </div>

        <!-- Kalender -->
        <div class="card">
            <h3>📅 Kalender</h3>
            <input type="date" style="padding: 8px; border-radius: 5px;">
        </div>

        <!-- Pengumuman -->
        <?php if ($latest_announcement): ?>
        <div class="card" style="background-color: #fef3c7;">
            <h3>📢 Pengumuman</h3>
            <p><?= htmlspecialchars($latest_announcement) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
