<?php
session_start();
require 'includes/db.php';
require 'includes/auth.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

// Ambil data user lengkap dari DB
$stmt = $conn->prepare("SELECT name, email, role, photo, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Tangani upload foto profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $filename = $user_id . '_' . basename($_FILES['profile_photo']['name']);
    $target_path = $upload_dir . $filename;

    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_path)) {
        $stmt = $conn->prepare("UPDATE users SET photo = ? WHERE id = ?");
        $stmt->bind_param("si", $target_path, $user_id);
        $stmt->execute();

        $_SESSION['user']['photo'] = $target_path;

        header("Location: profile.php");
        exit;
    }
}

// Tangani hapus foto profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_photo'])) {
    // Hapus file foto jika ada
    if (!empty($userData['photo']) && file_exists($userData['photo'])) {
        unlink($userData['photo']);
    }

    // Set null di database
    $stmt = $conn->prepare("UPDATE users SET photo = NULL WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $_SESSION['user']['photo'] = null;

    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="/lms/assets/css/style.css">
    <style>
        /* Sesuaikan agar tidak ada ruang kosong kiri kanan */
        .wrapper {
            display: flex;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .main {
            flex-grow: 1;
            padding: 40px 60px;
            background: #fff;
            max-width: 100%;
            box-sizing: border-box;
        }
        .card {
            background: #f9f9f9;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 0 8px rgb(0 0 0 / 0.05);
        }
        h3 {
            margin-top: 0;
            color: #6f5dca;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="file"], textarea {
            width: 100%;
            padding: 10px 15px;
            margin-top: 6px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
            box-sizing: border-box;
        }
        label {
            font-weight: 600;
            color: #4b2e83;
        }
        button.btn {
            padding: 12px 24px;
            background-color: #6f5dca;
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 700;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        button.btn:hover {
            background-color: #5a49a8;
        }
        .btn-danger {
            background-color: #e74c3c;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
        .alert-success, .alert-error {
            padding: 12px 18px;
            margin-bottom: 15px;
            border-radius: 10px;
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
        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .profile-header-left {
            display: flex;
            align-items: center;
        }
        .profile-photo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
            background: #dcd6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #6f5dca;
            font-size: 2.5rem;
            user-select: none;
        }
        a.btn-logout {
            padding: 6px 16px;
            font-size: 14px;
            background-color: #6f5dca;
            color: white;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        a.btn-logout:hover {
            background-color: #5a49a8;
        }
    </style>
</head>
<body>
   <div class="wrapper">
    <?php include 'includes/sidebar_' . strtolower($user['role']) . '.php'; ?>

    <div class="main">
        <div class="profile-header">
            <div class="profile-header-left">
                <?php if (!empty($userData['photo']) && file_exists($userData['photo'])): ?>
                    <img src="<?= htmlspecialchars($userData['photo']) ?>" alt="Foto Profil" class="profile-photo">
                <?php else: ?>
                    <div class="profile-photo"><?= strtoupper($userData['name'][0]) ?></div>
                <?php endif; ?>
                <h1 style="color:#6f5dca; margin:0;">My Profile</h1>
            </div>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>

        <!-- Informasi Akun -->
        <div class="card">
            <h3>Informasi Akun</h3>
            <p><strong>Nama:</strong> <?= htmlspecialchars($userData['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($userData['email']) ?></p>
            <p><strong>Role:</strong> <?= ucfirst(htmlspecialchars($userData['role'])) ?></p>
            <p><strong>Tanggal Pembuatan:</strong> <?= date("d M Y, H:i", strtotime($userData['created_at'])) ?></p>
        </div>

        <!-- Form Edit Nama & Email -->
        <div class="card">
            <h3>Edit Informasi Akun</h3>
            <?php if (isset($_SESSION['update_status'])): ?>
                <p class="<?= $_SESSION['update_status']['success'] ? 'alert-success' : 'alert-error' ?>">
                    <?= $_SESSION['update_status']['message'] ?>
                </p>
                <?php unset($_SESSION['update_status']); ?>
            <?php endif; ?>
            <form action="update_profile.php" method="POST">
                <label>Nama</label>
                <input type="text" name="name" value="<?= htmlspecialchars($userData['name']) ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required>

                <button type="submit" class="btn">Simpan Perubahan</button>
            </form>
        </div>

        <!-- Form Ubah Password -->
        <div class="card">
            <h3>Ubah Password</h3>
            <?php if (isset($_SESSION['password_status'])): ?>
                <p class="<?= $_SESSION['password_status']['success'] ? 'alert-success' : 'alert-error' ?>">
                    <?= $_SESSION['password_status']['message'] ?>
                </p>
                <?php unset($_SESSION['password_status']); ?>
            <?php endif; ?>
            <form action="update_password.php" method="POST">
                <label>Password Lama</label>
                <input type="password" name="old_password" required>

                <label>Password Baru</label>
                <input type="password" name="new_password" required>

                <button type="submit" class="btn">Simpan Perubahan</button>
            </form>
        </div>

        <!-- Form Upload Foto Profil -->
        <div class="card">
            <h3>Foto Profil</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_photo" accept="image/*" required><br><br>
                <button type="submit" class="btn">Unggah Foto</button>
            </form>
            <?php if (!empty($userData['photo'])): ?>
                <form method="POST" onsubmit="return confirm('Yakin ingin menghapus foto profil?')">
                    <input type="hidden" name="delete_photo" value="1">
                    <button type="submit" class="btn btn-danger" style="margin-top:15px;">Hapus Foto Profil</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>