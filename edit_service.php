<?php
session_start();

// Pastikan admin sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require 'db_connect.php';

// Ambil ID layanan
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $query->bind_param('i', $id);
    $query->execute();
    $result = $query->get_result();
    $service = $result->fetch_assoc();

    if (!$service) {
        echo "Layanan tidak ditemukan.";
        exit;
    }
} else {
    echo "ID layanan tidak diberikan.";
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nama_service = trim($_POST['nama_layanan']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = filter_var($_POST['harga'], FILTER_VALIDATE_FLOAT);

    // Validasi input
    if (empty($nama_service) || empty($deskripsi) || $harga === false) {
        $error = "Semua data harus diisi dengan benar.";
    } else {
        $gambar_lama = $service['gambar'];
        $gambar_baru = $gambar_lama;

        // Proses upload gambar baru
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $file_extension = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

            if (in_array($file_extension, $allowed_extensions)) {
                $gambar_name = uniqid('img_', true) . '.' . $file_extension;
                $gambar_path = 'images/' . $gambar_name;

                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $gambar_path)) {
                    $gambar_baru = $gambar_name;

                    // Hapus gambar lama jika ada
                    if ($gambar_lama && file_exists('images/' . $gambar_lama)) {
                        unlink('images/' . $gambar_lama);
                    }
                } else {
                    $error = "Gagal mengunggah gambar.";
                }
            } else {
                $error = "Ekstensi file tidak diperbolehkan.";
            }
        }

        // Update database
        if (!isset($error)) {
            $query = $conn->prepare("UPDATE services SET nama_layanan = ?, deskripsi = ?, harga = ?, gambar = ? WHERE id = ?");
            $query->bind_param('ssdsi', $nama_service, $deskripsi, $harga, $gambar_baru, $id);

            if ($query->execute()) {
                header("Location: admin_dashboard.php?success=Layanan berhasil diperbarui");
                exit;
            } else {
                $error = "Gagal memperbarui layanan.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Layanan</title>
    <link rel="stylesheet" href="style2.css">
    <style>
        /* Tambahkan gaya untuk preview container */
        #preview-container {
            margin-top: 10px;
            border: 1px solid #ccc;
            padding: 10px;
            max-width: 120px;
            text-align: center;
        }

        #preview-container img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<div class="navbar">
    <h2>Edit Layanan - Klinik Kecantikan</h2>
    <div class="navbar-links">
        <a href="admin_dashboard.php">Kembali</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h1>Edit Layanan</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="edit_service.php?id=<?= htmlspecialchars($id) ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($service['id']) ?>">

        <label for="nama_layanan">Nama Layanan:</label>
        <input type="text" id="nama_layanan" name="nama_layanan" value="<?= htmlspecialchars($service['nama_layanan']) ?>" required>

        <label for="deskripsi">Deskripsi:</label>
        <textarea id="deskripsi" name="deskripsi" required><?= htmlspecialchars($service['deskripsi']) ?></textarea>

        <label for="harga">Harga:</label>
        <input type="number" id="harga" name="harga" value="<?= htmlspecialchars($service['harga']) ?>" required>

        <label for="gambar">Gambar (Opsional):</label>
        <input type="file" id="gambar" name="gambar">
        
        <div id="preview-container">
            <!-- Preview gambar baru -->
            <img id="preview-image" src="" alt="" style="display: none;">
        </div>

        <button type="submit">Simpan Perubahan</button>
    </form>
</div>

<script>
    document.getElementById('gambar').addEventListener('change', function(event) {
        const previewImage = document.getElementById('preview-image');
        const file = event.target.files[0];
        
        if (file) {
            previewImage.src = URL.createObjectURL(file);
            previewImage.style.display = 'block'; // Tampilkan gambar
        } else {
            previewImage.style.display = 'none'; // Sembunyikan jika tidak ada file
        }
    });
</script>
</body>
</html>
