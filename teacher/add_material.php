<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

include '../includes/db.php';

$upload_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $title = $_POST['title'];
    $link = $_POST['link'] ?? '';
    $file_path = '';

    if (!empty($_FILES['file']['name'])) {
        $upload_dir = '../uploads/';
        $filename = basename($_FILES['file']['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file_path = $filename;
        } else {
            $upload_message = '<div class="alert-error">❌ Gagal upload file.</div>';
        }
    }

    if (empty($upload_message)) {
        $stmt = $conn->prepare("INSERT INTO materials (class_id, title, file_path, link) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $class_id, $title, $file_path, $link);
        $stmt->execute();
        $upload_message = '<div class="alert-success">✅ Materi berhasil diunggah!</div>';
    }
}

include '../includes/header.php';
?>

<div class="wrapper">
    <?php include '../includes/sidebar_guru.php'; ?>

    <div class="main">
        <h2>📚 Upload Materi</h2>

        <?= $upload_message ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="class_id">Kelas:</label>
            <select name="class_id" id="class_id" required>
                <?php
                $guru_id = $_SESSION['user']['id'];
                $result = $conn->query("SELECT * FROM classes WHERE teacher_id = $guru_id");
                while ($row = $result->fetch_assoc()):
                ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="title">Judul Materi:</label>
            <input type="text" id="title" name="title" placeholder="Contoh: Algoritma Pencarian" required>

            <label for="file">Upload PDF (opsional):</label>
            <input type="file" id="file" name="file" accept=".pdf">

            <label for="link">Atau Link (opsional):</label>
            <input type="text" id="link" name="link" placeholder="https://contoh.com/materi">

            <button type="submit">Upload</button>
        </form>
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
        background-color:rgb(255, 255, 255);
    }

    h2 {
        color: #6c4bcc;
        font-size: 1.8rem;
        margin-bottom: 25px;
    }

    form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #4b2e83;
    }

    form input[type="text"],
    form input[type="file"],
    form select {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 1rem;
        background-color: #fbfaff;
    }

    form input[type="text"]:focus,
    form input[type="file"]:focus,
    form select:focus {
        outline: none;
        border-color: #9a7eea;
        box-shadow: 0 0 0 3px rgba(154, 126, 234, 0.2);
    }

    button[type="submit"] {
        width: 100%;
        padding: 14px;
        background-color: #9a7eea;
        color: white;
        border: none;
        border-radius: 12px;
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
        margin-bottom: 20px;
        border-radius: 12px;
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

    .back-link {
        display: inline-block;
        margin-top: 12px;
        text-decoration: none;
        color: #4b2e83;
        font-weight: 600;
    }

    .back-link:hover {
        text-decoration: underline;
    }
</style>
