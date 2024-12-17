<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require 'db_connect.php';

// Ambil semua layanan dari database
$services = $conn->query("SELECT * FROM services");

// Proses Tambah Reservasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $nama = $_POST['nama'];
    $layanan = $_POST['layanan'];
    $tanggal = $_POST['tanggal'];

    // Proses Upload Gambar
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $gambar_name = basename($_FILES["gambar"]["name"]);
    $gambar_path = $target_dir . time() . "_" . $gambar_name;
    $uploadOk = true;

    $imageFileType = strtolower(pathinfo($gambar_path, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["gambar"]["tmp_name"]);
    if ($check === false || $_FILES["gambar"]["size"] > 5000000 || !in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
        $uploadOk = false;
        $error_msg = "File tidak valid atau terlalu besar.";
    }

    if ($uploadOk) {
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $gambar_path);
        $stmt = $conn->prepare("INSERT INTO reservations (nama, layanan, tanggal, gambar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $layanan, $tanggal, $gambar_path);
        if ($stmt->execute()) {
            header("Location: admin_reservasi.php?success=Reservasi berhasil ditambahkan.");
        } else {
            header("Location: admin_reservasi.php?error=Gagal menambahkan reservasi.");
        }
        exit;
    } else {
        header("Location: admin_reservasi.php?error=" . urlencode($error_msg));
        exit;
    }
}

// Ambil semua data reservasi
$reservasi = $conn->query("SELECT * FROM reservations");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Reservasi Janji</title>
</head>
<body>
    <div class="navbar">
        <h2>Reservasi Janji</h2>
        <a href="admin_dashboard.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <p style="text-align: center; color: green;"><?= htmlspecialchars($_GET['success']) ?></p>
    <?php elseif (isset($_GET['error'])): ?>
        <p style="text-align: center; color: red;"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <!-- Form Tambah Reservasi -->
    <div class="form-container">
        <h2>Buat Janji Baru</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="nama">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" required>

            <label for="layanan">Layanan</label>
            <select name="layanan" id="layanan" required>
                <?php while ($service = $services->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($service['nama_layanan']) ?>">
                        <?= htmlspecialchars($service['nama_layanan']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="tanggal">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" required>

            <label for="gambar">Upload Gambar</label>
            <input type="file" name="gambar" id="gambar" accept="image/*" required>

            <button type="submit" name="add">Kirim</button>
        </form>
    </div>

    <!-- Tabel Daftar Reservasi -->
    <h1>Daftar Reservasi</h1>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Layanan</th>
                <th>Tanggal</th>
                <th>Gambar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $reservasi->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['layanan']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td><img src="<?= htmlspecialchars($row['gambar']) ?>" alt="Gambar Reservasi" style="width:100px;"></td>
                    <td>
                        <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus reservasi ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
