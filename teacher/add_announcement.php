<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

include '../includes/db.php';

$feedback = null;

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO announcements (message, created_at) VALUES (?, NOW())");
        $stmt->bind_param("s", $message);
        $stmt->execute();

        $feedback = '<div class="alert-success">✅ Pengumuman berhasil ditambahkan!</div>';
    } else {
        $feedback = '<div class="alert-error">❌ Pengumuman tidak boleh kosong!</div>';
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="wrapper">
    <?php include '../includes/sidebar_guru.php'; ?>

    <div class="main">
        <h2>📢 Tambah Pengumuman</h2>
        <p class="desc">Gunakan fitur ini untuk menyampaikan informasi penting kepada seluruh siswa. Pengumuman akan langsung muncul di dashboard mereka.</p>

        <?= $feedback ?>

        <form method="POST">
            <label for="message">📝 Isi Pengumuman:</label>
            <textarea id="message" name="message" rows="4" placeholder="Contoh: Hari Jumat 5 Mei tidak ada kelas karena..." required></textarea>
            <button type="submit">➕ Tambah Pengumuman</button>
        </form>

        <h3 style="margin-top: 40px;">📃 Pengumuman Terbaru</h3>
        <ul class="announcement-list">
            <?php
            $result = $conn->query("SELECT message, created_at FROM announcements ORDER BY created_at DESC LIMIT 5");
            if ($result->num_rows === 0) {
                echo "<li><em>Belum ada pengumuman.</em></li>";
            } else {
                while ($row = $result->fetch_assoc()) {
                    echo "<li><span class='date'>" . date('d M Y H:i', strtotime($row['created_at'])) . "</span> - " . htmlspecialchars($row['message']) . "</li>";
                }
            }
            ?>
        </ul>
    </div>
</div>
<?php include '../includes/footer.php'; ?>

<style>
    .wrapper {
        display: flex;
        min-height: 100vh;
    }

    .main {
        padding: 40px 60px;
        flex-grow: 1;
        background-color: #fdfcff;
    }

    h2 {
        color: #6c4bcc;
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    .desc {
        font-size: 0.95rem;
        color: #555;
        margin-bottom: 25px;
    }

    form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #4b2e83;
    }

    form textarea {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #ccc;
        font-size: 1rem;
        background-color: #fff;
        margin-bottom: 20px;
    }

    form textarea:focus {
        outline: none;
        border-color: #9a7eea;
        box-shadow: 0 0 0 3px rgba(154, 126, 234, 0.2);
    }

    button[type="submit"] {
        padding: 12px 24px;
        background-color: #9a7eea;
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
        background-color: #7a5fcb;
    }

    .alert-success,
    .alert-error {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 0.95rem;
    }

    .alert-success {
        background-color: #e4fce8;
        border-left: 5px solid #2ecc71;
        color: #256a3a;
    }

    .alert-error {
        background-color: #fde6e8;
        border-left: 5px solid #e74c3c;
        color: #9e2a2b;
    }

    .announcement-list {
        list-style: none;
        padding-left: 0;
        margin-top: 15px;
    }

    .announcement-list li {
        padding: 10px 0;
        border-bottom: 1px solid #ddd;
        font-size: 0.95rem;
        color: #333;
    }

    .announcement-list .date {
        font-weight: bold;
        color: #6c4bcc;
        margin-right: 8px;
    }
</style>
