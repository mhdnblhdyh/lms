<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    header("Location: ../login.php");
    exit;
}

include '../includes/db.php';
include '../includes/header.php';

$student_id = $_SESSION['user']['id'];
$name = $_SESSION['user']['name'];

// Ambil foto profil dari database
$user_photo_result = $conn->query("SELECT photo FROM users WHERE id = $student_id");
$user_photo_data = $user_photo_result->fetch_assoc();
$user_photo = $user_photo_data['photo'] ?? null;

// Hitung jumlah kelas
$kelas_result = $conn->query("SELECT COUNT(*) AS total FROM class_members WHERE student_id = $student_id");
$kelas_total = $kelas_result->fetch_assoc()['total'];

// Tugas deadline terdekat
$tugas_result = $conn->query("
    SELECT a.title, c.name AS class_name, a.deadline
    FROM assignments a
    JOIN classes c ON a.class_id = c.id
    JOIN class_members cm ON cm.class_id = c.id
    WHERE cm.student_id = $student_id AND a.deadline >= CURDATE()
    ORDER BY a.deadline ASC LIMIT 3
");

// Progres pengumpulan tugas
$submitted = $conn->query("SELECT COUNT(*) AS jumlah FROM submissions WHERE student_id = $student_id")->fetch_assoc()['jumlah'];
$total = $conn->query("
    SELECT COUNT(*) AS total
    FROM assignments a
    JOIN class_members cm ON a.class_id = cm.class_id
    WHERE cm.student_id = $student_id
")->fetch_assoc()['total'];

// Ambil pengumuman terbaru (jika ada)
$pengumuman = $conn->query("SELECT message FROM announcements ORDER BY created_at DESC LIMIT 1");
$latest_announcement = $pengumuman && $pengumuman->num_rows > 0 ? $pengumuman->fetch_assoc()['message'] : null;
?>

<div class="wrapper">
    <?php include '../includes/sidebar_siswa.php'; ?>

    <div class="main">
        <!-- Header -->
        <div class="top-bar" style="position: relative; margin-bottom: 30px;">
            
            <div class="logo-title">
                <h1 style="font-size: 32px; font-weight: 800; color: #6f5dca;">📚 Mini LMS</h1>
            </div>
             <h2>Dashboard Siswa</h2>

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

        <!-- Konten Dashboard -->
        <div class="card">
            <p>Selamat datang, <strong><?= htmlspecialchars($name) ?></strong>!</p>
            <p>Silakan pilih menu di sidebar untuk mulai belajar.</p>
        </div>

        <div class="card">
            <h3>📘 Kelas yang Diikuti</h3>
            <p>Total: <strong><?= $kelas_total ?></strong> kelas</p>
        </div>

        <div class="card">
            <h3>🕒 Deadline Tugas Terdekat</h3>
            <?php if ($tugas_result->num_rows > 0): ?>
                <ul>
                    <?php while ($row = $tugas_result->fetch_assoc()): ?>
                        <li><strong><?= htmlspecialchars($row['title']) ?></strong> - <?= htmlspecialchars($row['class_name']) ?> (<?= $row['deadline'] ?>)</li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Tidak ada tugas mendekati deadline.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3>📈 Progres Pengumpulan Tugas</h3>
            <p><?= $submitted ?> dari <?= $total ?> tugas sudah dikumpulkan.</p>
        </div>

        <div class="card">
            <h3>🗕 Kalender</h3>
            <input type="date" style="padding: 8px; border-radius: 5px;">
        </div>

        <?php if ($latest_announcement): ?>
            <div class="card" style="background-color: #fef3c7;">
                <h3>📢 Pengumuman</h3>
                <p><?= htmlspecialchars($latest_announcement) ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
