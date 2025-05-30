<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

include '../includes/db.php';

$teacher_id = $_SESSION['user']['id'];
$message = '';

// Proses update nilai
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submission_id = $_POST['submission_id'];
    $grade = $_POST['grade'];

    $stmt = $conn->prepare("UPDATE submissions SET grade = ? WHERE id = ?");
    $stmt->bind_param("ii", $grade, $submission_id);
    $stmt->execute();

    $message = '<div class="alert-success">✅ Nilai berhasil disimpan!</div>';
}

// Ambil semua submission dari tugas yang dibuat guru
$query = "
    SELECT s.id AS submission_id, u.name AS student_name, c.name AS class_name,
           a.title AS assignment_title, s.file_path, s.grade
    FROM submissions s
    JOIN assignments a ON s.assignment_id = a.id
    JOIN classes c ON a.class_id = c.id
    JOIN users u ON s.student_id = u.id
    WHERE c.teacher_id = ?
    ORDER BY s.submitted_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$submissions = $result->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="wrapper">
    <?php include '../includes/sidebar_guru.php'; ?>

    <main class="main">
        <h2>🧑‍🏫 Penilaian Tugas Siswa</h2>

        <?= $message ?>

        <?php if (count($submissions) === 0): ?>
            <p>Belum ada tugas yang dikumpulkan.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Judul Tugas</th>
                        <th>Jawaban</th>
                        <th>Nilai</th>
                        <th>Penilaian</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($submissions as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['student_name']) ?></td>
                        <td><?= htmlspecialchars($s['class_name']) ?></td>
                        <td><?= htmlspecialchars($s['assignment_title']) ?></td>
                        <td>
                            <a href="../uploads/submissions/<?= urlencode($s['file_path']) ?>" target="_blank">📄 Lihat</a>
                        </td>
                        <td><?= $s['grade'] !== null ? $s['grade'] : '-' ?></td>
                        <td>
                            <form method="POST" style="display: flex; gap: 8px; align-items: center;">
                                <input type="hidden" name="submission_id" value="<?= $s['submission_id'] ?>">
                                <input type="number" name="grade" min="0" max="100" required>
                                <button type="submit">Simpan</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

       
    </main>
</div>

<?php include '../includes/footer.php'; ?>


   <style>
    /* Layout utama */
    .wrapper {
        display: flex;
        min-height: 100vh;
        background: linear-gradient(135deg, #e9e4f0, #f4f0ff);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #3a2a6a;
    }

    .main {
        flex-grow: 1;
        padding: 40px 60px;
        overflow-x: auto;
        max-width: 100%;
    }

    h2 {
        font-size: 2.4rem;
        font-weight: 900;
        margin-bottom: 40px;
        text-align: center;
        letter-spacing: 1.1px;
        color: #5a3eaa;
        text-shadow: 1px 1px 3px rgba(90, 62, 170, 0.3);
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
        background: transparent;
        min-width: 720px;
    }

    thead tr {
        background-color: #d3c7ff;
        color: #4b2e83;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        font-weight: 700;
        box-shadow: 0 2px 5px rgba(75, 46, 131, 0.15);
        border-radius: 12px;
    }

    thead th {
        padding: 14px 20px;
    }

    tbody tr {
        background: white;
        border-radius: 14px;
        box-shadow: 0 4px 15px rgba(100, 77, 170, 0.1);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        cursor: default;
    }

    tbody tr:hover {
        box-shadow: 0 10px 25px rgba(100, 77, 170, 0.25);
        transform: translateY(-5px);
    }

    tbody td {
        padding: 15px 20px;
        vertical-align: middle;
    }

    a {
        color: #7b59f9;
        font-weight: 700;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    a:hover {
        color: #5030e5;
        text-decoration: underline;
    }

    input[type="number"] {
        width: 80px;
        padding: 8px 12px;
        font-size: 1rem;
        border-radius: 12px;
        border: 2px solid #d1c4ff;
        outline: none;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        font-weight: 600;
        color: #4b2e83;
    }

    input[type="number"]:focus {
        border-color: #6c4bcc;
        box-shadow: 0 0 6px #9a7eea88;
        background-color: #f8f6ff;
    }

    button {
        padding: 8px 16px;
        background: linear-gradient(135deg, #7a5fcb, #9a7eea);
        border: none;
        border-radius: 14px;
        color: white;
        font-weight: 900;
        font-size: 1rem;
        cursor: pointer;
        box-shadow: 0 6px 15px rgba(122, 95, 203, 0.6);
        transition: background 0.4s ease, transform 0.2s ease;
        user-select: none;
    }

    button:hover {
        background: linear-gradient(135deg, #5030e5, #7a5fcb);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(80, 48, 229, 0.8);
    }

    .alert-success {
        margin-bottom: 28px;
        padding: 18px 24px;
        background-color: #d4edda;
        border-left: 6px solid #28a745;
        border-radius: 16px;
        color: #27632a;
        font-weight: 700;
        font-size: 1.05rem;
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.15);
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }

    .back-link {
        display: inline-block;
        margin-top: 38px;
        color: #5a3eaa;
        font-weight: 700;
        font-size: 1.15rem;
        text-decoration: underline;
        transition: color 0.3s ease;
    }

    .back-link:hover {
        color: #7b59f9;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .main {
            padding: 30px 20px;
        }
        table {
            min-width: 600px;
        }
        input[type="number"] {
            width: 70px;
            font-size: 0.95rem;
        }
        button {
            padding: 7px 14px;
            font-size: 0.95rem;
        }
    }

    @media (max-width: 480px) {
        table {
            min-width: 480px;
        }
        input[type="number"] {
            width: 60px;
            font-size: 0.9rem;
        }
        button {
            padding: 6px 12px;
            font-size: 0.9rem;
        }
        h2 {
            font-size: 1.8rem;
            margin-bottom: 25px;
        }
    }
</style>


