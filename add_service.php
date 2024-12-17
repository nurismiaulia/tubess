<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require 'db_connect.php';

// Pastikan koneksi berhasil
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Proses Tambah Data Reservasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'];
    $reservation_date = $_POST['reservation_date'];
    $status = $_POST['status'];

    // Validasi input
    if (!empty($service_id) && !empty($reservation_date) && !empty($status)) {
        // Insert data reservasi ke database
        $stmt = $conn->prepare("INSERT INTO reservations (service_id, reservation_date, status) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $service_id, $reservation_date, $status);

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php?success=Reservasi berhasil ditambahkan");
            exit;
        } else {
            $error = "Gagal menambahkan reservasi.";
        }
    } else {
        $error = "Semua kolom harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Reservasi</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
<div class="navbar">
    <h2>Tambah Reservasi - Klinik Kecantikan</h2>
    <div class="navbar-links">
        <a href="admin_dashboard.php">Kembali</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<h1>Tambah Reservasi Baru</h1>

<!-- Notifikasi -->
<?php
if (isset($error)) {
    echo "<p class='alert'>$error</p>";
}
?>

<form action="add_reservation.php" method="POST">
    <label for="service_id">Pilih Layanan:</label>
    <select id="service_id" name="service_id" required>
        <?php
        // Ambil data layanan dari database
        $services_query = "SELECT * FROM services";
        $services_result = $conn->query($services_query);
        while ($service = $services_result->fetch_assoc()) {
            echo "<option value='{$service['id']}'>{$service['nama_layanan']}</option>";
        }
        ?>
    </select>

    <label for="reservation_date">Tanggal Reservasi:</label>
    <input type="date" id="reservation_date" name="reservation_date" required>

    <label for="status">Status:</label>
    <select id="status" name="status" required>
        <option value="pending">Pending</option>
        <option value="confirmed">Confirmed</option>
        <option value="cancelled">Cancelled</option>
    </select>

    <button type="submit">Tambah Reservasi</button>
</form>

</body>
</html>
