<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

include '../includes/db.php';
$create_message = '';

function generateClassCode($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $code;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $teacher_id = $_SESSION['user']['id'];

    // Generate kode unik
    do {
        $code = generateClassCode();
        $check = $conn->prepare("SELECT id FROM classes WHERE code = ?");
        $check->bind_param("s", $code);
        $check->execute();
        $result = $check->get_result();
    } while ($result->num_rows > 0);

    $stmt = $conn->prepare("INSERT INTO classes (name, code, teacher_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $code, $teacher_id);
    $stmt->execute();

    $create_message = '<div class="alert-success">✅ Kelas berhasil dibuat! Kode: <strong>' . htmlspecialchars($code) . '</strong></div>';
}

include '../includes/header.php';
?>

<div class="wrapper">
    <?php include '../includes/sidebar_guru.php'; ?>

    <div class="main">
        <div class="header">
            <h2>🏫 Buat Kelas Baru</h2>
            <div class="welcome">
                Hai, <?= htmlspecialchars($_SESSION['user']['name']) ?> |
                <a href="../logout.php">Logout</a>
            </div>
        </div>

        <p class="desc">Masukkan nama kelas yang ingin kamu buat. Kode kelas akan dibuat otomatis dan dapat dibagikan kepada siswa.</p>

        <?= $create_message ?>

        <form method="POST">
            <label for="name">📝 Nama Kelas:</label>
            <input type="text" id="name" name="name" placeholder="Contoh: Matematika XI IPA" required>

            <button type="submit">➕ Buat Kelas</button>
        </form>

        <h3 style="margin-top: 40px;">📚 Daftar Kelas Kamu</h3>
        <ul class="class-list">
            <?php
            $teacher_id = $_SESSION['user']['id'];
            $result = $conn->query("SELECT name, code FROM classes WHERE teacher_id = $teacher_id ORDER BY id DESC");

            if ($result->num_rows === 0) {
                echo "<p><em>Belum ada kelas yang dibuat.</em></p>";
            } else {
                while ($row = $result->fetch_assoc()) {
                    echo "<li><strong>" . htmlspecialchars($row['name']) . "</strong> — <code>" . htmlspecialchars($row['code']) . "</code></li>";
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
        background-color: #f9f8fd;
    }

    .header {
        margin-bottom: 25px;
    }

    .header h2 {
        color: #6c4bcc;
        font-size: 2rem;
        margin-bottom: 5px;
    }

    .welcome {
        font-size: 0.95rem;
        color: #4b4559;
    }

    .welcome a {
        color: #9c88ff;
        text-decoration: none;
        font-weight: 600;
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

    form input[type="text"] {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 1rem;
        background-color: #fff;
    }

    form input[type="text"]:focus {
        outline: none;
        border-color: #9a7eea;
        box-shadow: 0 0 0 3px rgba(154, 126, 234, 0.2);
    }

    button[type="submit"] {
        padding: 14px 24px;
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

    .alert-success {
        background-color: #e4fce8;
        border-left: 5px solid #2ecc71;
        color: #256a3a;
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .class-list {
        list-style: none;
        padding-left: 0;
        margin-top: 15px;
    }

    .class-list li {
        padding: 10px 0;
        border-bottom: 1px solid #ddd;
        font-size: 1rem;
    }

    .class-list code {
        background-color: #eee;
        padding: 2px 6px;
        border-radius: 5px;
        font-family: monospace;
        font-size: 0.9rem;
        margin-left: 6px;
    }
</style>
