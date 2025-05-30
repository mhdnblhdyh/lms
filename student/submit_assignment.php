<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    header("Location: ../login.php");
    exit;
}

include '../includes/db.php';

$student_id = $_SESSION['user']['id'];
$message = '';

// Ambil daftar tugas dari kelas yang diikuti siswa
$query = "
    SELECT a.id, a.title, c.name AS class_name 
    FROM assignments a
    JOIN classes c ON a.class_id = c.id
    JOIN class_members cm ON cm.class_id = c.id
    WHERE cm.student_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$assignments = $result->fetch_all(MYSQLI_ASSOC);

// Proses upload file tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_id = $_POST['assignment_id'];
    $file_path = '';

    if (!empty($_FILES['file']['name'])) {
        $upload_dir = '../uploads/submissions/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = time() . '_' . basename($_FILES['file']['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file_path = $filename;

            $stmt = $conn->prepare("INSERT INTO submissions (assignment_id, student_id, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $assignment_id, $student_id, $file_path);
            $stmt->execute();

            $message = '<div class="alert-success">✅ Tugas berhasil dikumpulkan!</div>';
        } else {
            $message = '<div class="alert-error">❌ Gagal upload file.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kumpulkan Tugas</title>
    <style>
        body {
            background-color: #f4f0ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(75, 46, 131, 0.1);
        }
        h2 {
            color: #6c4bcc;
            margin-bottom: 25px;
            text-align: center;
        }
        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #333;
        }
        select, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            background-color: #9a7eea;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            display: block;
            width: 100%;
        }
        button:hover {
            background-color: #7a5fcb;
        }
        .alert-success {
            margin-bottom: 20px;
            padding: 15px 20px;
            background-color: #d4edda;
            border-left: 5px solid #28a745;
            border-radius: 10px;
            color: #27632a;
        }
        .alert-error {
            margin-bottom: 20px;
            padding: 15px 20px;
            background-color: #f8d7da;
            border-left: 5px solid #dc3545;
            border-radius: 10px;
            color: #842029;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>📝 Kumpulkan Tugas</h2>
    <?= $message ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="assignment_id">Pilih Tugas:</label>
        <select name="assignment_id" id="assignment_id" required>
            <?php foreach ($assignments as $a): ?>
                <option value="<?= $a['id'] ?>">
                    <?= htmlspecialchars($a['title']) ?> - <?= htmlspecialchars($a['class_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="file">Upload File Jawaban:</label>
        <input type="file" name="file" id="file" required>

        <button type="submit">Kumpulkan</button>
    </form>
      <a href="dashboard.php" class="back-link">⬅ Kembali ke Dashboard</a>
</div>

</body>
</html>
