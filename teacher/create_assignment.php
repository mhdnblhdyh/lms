<?php 
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

$teacher_id = $_SESSION['user']['id'];
$classes = $conn->query("SELECT * FROM classes WHERE teacher_id = $teacher_id");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $desc = htmlspecialchars($_POST['description']);
    $deadline = $_POST['deadline'];
    $class_id = $_POST['class_id'];

    $stmt = $conn->prepare("INSERT INTO assignments (class_id, title, description, deadline) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $class_id, $title, $desc, $deadline);

    if ($stmt->execute()) {
        $message = '<div class="alert-success">✅ Tugas berhasil dibuat! <br><a href="dashboard.php" class="back-link">⬅ Kembali ke Dashboard</a></div>';
    } else {
        $message = '<div class="alert-error">❌ Gagal membuat tugas: ' . $stmt->error . '</div>';
    }
}

include '../includes/header.php';
?>

<div class="wrapper">
    <?php include '../includes/sidebar_guru.php'; ?>

    <div class="main">
        <h2>📝 Buat Tugas Baru</h2>

        <?= $message ?>

        <form method="POST">
            <label for="title">Judul Tugas:</label>
            <input type="text" name="title" id="title" placeholder="Contoh: Tugas Array" required>

            <label for="description">Deskripsi:</label>
            <textarea name="description" id="description" placeholder="Berikan penjelasan tugas di sini" required></textarea>

            <label for="deadline">Batas Waktu:</label>
            <input type="date" name="deadline" id="deadline" required>

            <label for="class_id">Pilih Kelas:</label>
            <select name="class_id" id="class_id" required>
                <option value="">-- Pilih Kelas --</option>
                <?php while ($row = $classes->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Buat Tugas</button>
        </form>

        <a href="dashboard.php" class="back-link">⬅ Kembali ke Dashboard</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<style>
    /* Layout utama */
    .wrapper {
        display: flex;
        min-height: 100vh;
        background-color: #f5f0ff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .main {
        flex-grow: 1;
        padding: 40px 60px;
        color: #333;
    }

    h2 {
        color: #6c4bcc;
        font-size: 2rem;
        margin-bottom: 30px;
        font-weight: 700;
    }

    form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #4b2e83;
    }

    input[type="text"],
    input[type="date"],
    textarea,
    select {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 12px;
        font-size: 1rem;
        background-color: #fbfaff;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: #9a7eea;
        box-shadow: 0 0 0 3px rgba(154, 126, 234, 0.2);
    }

    textarea {
        resize: vertical;
        height: 120px;
    }

    button[type="submit"] {
        width: 100%;
        padding: 14px;
        background-color: #9a7eea;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    button[type="submit"]:hover {
        background-color: #7a5fcb;
        transform: translateY(-1px);
    }

    .alert-success,
    .alert-error {
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 0.95rem;
    }

    .alert-success {
        background-color: #d6f3d6;
        border-left: 5px solid #28a745;
        color: #27632a;
    }

    .alert-error {
        background-color: #f8d7da;
        border-left: 5px solid #dc3545;
        color: #721c24;
    }

    .back-link {
        display: inline-block;
        margin-top: 10px;
        color: #4b2e83;
        font-weight: 700;
        text-decoration: underline;
    }
</style>
