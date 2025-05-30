<?php
session_start();
include '../includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
    header("Location: ../login.php");
    exit;
}
$student_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("
SELECT a.id, a.title, a.description, a.due_date, c.name AS class_name,
       (SELECT id FROM submissions WHERE assignment_id = a.id AND student_id = ?) AS submitted
FROM assignments a
JOIN classes c ON a.class_id = c.id
JOIN enrollments e ON e.class_id = c.id
WHERE e.student_id = ?
ORDER BY a.due_date ASC
");
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Daftar Tugas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        .main {
            flex: 1;
            padding: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 1.8rem;
            color: #6c4bcc;
        }

        .welcome {
            font-size: 1rem;
            color: #555;
        }

        .welcome a {
            color: #9c88ff;
            text-decoration: none;
            margin-left: 10px;
            font-weight: 600;
        }

        .assignment-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(75, 46, 131, 0.15);
        }

        .assignment-table thead {
            background-color: #9a7eea;
            color: white;
        }

        .assignment-table th, .assignment-table td {
            padding: 14px 20px;
            text-align: left;
        }

        .assignment-table tbody tr {
            border-bottom: 1px solid #eee;
            background-color: #f9f7ff;
        }

        .assignment-table tbody tr:hover {
            background-color: #e6e0ff;
        }

        .status-submitted {
            background-color: #a6d8a8;
            color: #2a662a;
            padding: 6px 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-pending {
            background-color: #f4c1c1;
            color: #7a1a1a;
            padding: 6px 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .submit-link {
            background-color: #9c88ff;
            color: white;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
        }

        .submit-link:hover {
            background-color: #7a5fcb;
        }

        @media(max-width: 700px) {
            .main {
                padding: 20px;
            }

            .assignment-table thead {
                display: none;
            }

            .assignment-table, .assignment-table tbody, .assignment-table tr, .assignment-table td {
                display: block;
                width: 100%;
            }

            .assignment-table tr {
                margin-bottom: 15px;
                background: #fff;
                box-shadow: 0 2px 8px rgba(75, 46, 131, 0.1);
                border-radius: 12px;
                padding: 15px;
            }

            .assignment-table td {
                padding-left: 50%;
                position: relative;
                text-align: right;
            }

            .assignment-table td::before {
                position: absolute;
                left: 20px;
                top: 14px;
                width: 45%;
                white-space: nowrap;
                font-weight: 600;
                color: #6c4bcc;
                content: attr(data-label);
                text-align: left;
            }

            .submit-link {
                display: inline-block;
                margin-top: 8px;
                text-align: center;
                width: 100%;
                padding: 10px 0;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include '../includes/sidebar_siswa.php'; ?>

    <div class="main">
        <div class="header">
            <h1>📋 Tugas Kamu</h1>
            <div class="welcome">
                Hai, <?= htmlspecialchars($_SESSION['user']['name']) ?> |
                <a href="../logout.php">Logout</a>
            </div>
        </div>

        <?php if ($result->num_rows === 0): ?>
            <p style="color:#7a5fcb;">Belum ada tugas yang tersedia.</p>
        <?php else: ?>
            <table class="assignment-table">
                <thead>
                <tr>
                    <th>Kelas</th>
                    <th>Judul</th>
                    <th>Batas Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Kelas"><?= htmlspecialchars($row['class_name']) ?></td>
                        <td data-label="Judul"><?= htmlspecialchars($row['title']) ?></td>
                        <td data-label="Batas Waktu"><?= htmlspecialchars($row['due_date']) ?></td>
                        <td data-label="Status">
                            <?php if ($row['submitted']): ?>
                                <span class="status-submitted">Sudah</span>
                            <?php else: ?>
                                <span class="status-pending">Belum</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Aksi">
                            <a href="submit_assignment.php?id=<?= $row['id'] ?>" class="submit-link">Kumpulkan</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
