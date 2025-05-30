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
    <meta charset="UTF-8" />
    <title>Nilai Saya</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        table.table {
            width: 100%;
            border-collapse: collapse;
            font-size: 1rem;
            margin-top: 30px;
            box-shadow: 0 4px 15px rgba(75, 46, 131, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        table.table thead {
            background: #9c88ff;
            color: white;
        }

        table.table th, table.table td {
            padding: 14px 20px;
            text-align: left;
        }

        table.table tbody tr {
            background: #f9f7ff;
            border-bottom: 1px solid #e0dbf6;
        }

        table.table tbody tr:hover {
            background: #eee9ff;
        }

        .logo-title {
            margin-top: 40px;
            font-size: 1.4rem;
            color: #6c4bcc;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            table.table thead {
                display: none;
            }

            table.table, table.table tbody, table.table tr, table.table td {
                display: block;
                width: 100%;
            }

            table.table tr {
                margin-bottom: 15px;
                background: #fff;
                box-shadow: 0 2px 8px rgba(75, 46, 131, 0.1);
                border-radius: 12px;
                padding: 15px;
            }

            table.table td {
                padding-left: 50%;
                position: relative;
                text-align: right;
            }

            table.table td::before {
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
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include '../includes/sidebar_siswa.php'; ?>

    <div class="main">
        <div class="header">
            <h1>📊 Nilai Saya</h1>
            <div class="welcome">
                Hai, <?= htmlspecialchars($_SESSION['user']['name']) ?> |
                <a href="../logout.php">Logout</a>
            </div>
        </div>

        <?php
        $student_id = $_SESSION['user']['id'];
        $stmt = $conn->prepare("
            SELECT a.title, c.name AS class_name, s.grade
            FROM submissions s
            JOIN assignments a ON s.assignment_id = a.id
            JOIN classes c ON a.class_id = c.id
            WHERE s.student_id = ?
        ");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>

        <h2 class="logo-title">Daftar Nilai Tugas</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Kelas</th>
                    <th>Judul Tugas</th>
                    <th>Nilai</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows === 0): ?>
                <tr>
                    <td colspan="3" style="text-align:center; padding:20px; color:#7a5fcb;">
                        Belum ada nilai yang tersedia.
                    </td>
                </tr>
            <?php else: ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Kelas"><?= htmlspecialchars($row['class_name']) ?></td>
                        <td data-label="Tugas"><?= htmlspecialchars($row['title']) ?></td>
                        <td data-label="Nilai"><?= is_null($row['grade']) ? 'Belum dinilai' : htmlspecialchars($row['grade']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
