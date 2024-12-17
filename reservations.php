<?php
session_start();

// Pastikan pengguna sudah login dan memiliki peran admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require 'db_connect.php';

// Proses Tambah Reservasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $service_id = $_POST['service_id'];
    $reservation_date = $_POST['reservation_date'];
    $status = $_POST['status'];

    // Validasi input
    if (!empty($customer_name) && !empty($service_id) && !empty($reservation_date) && !empty($status)) {
        // Insert data reservasi ke database
        $stmt = $conn->prepare("INSERT INTO reservations (customer_name, service_id, reservation_date, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $customer_name, $service_id, $reservation_date, $status);

        if ($stmt->execute()) {
            // Jika sukses, arahkan ke halaman dengan pesan sukses
            $success_message = "Reservasi berhasil ditambahkan.";
        } else {
            $error = "Gagal menambahkan reservasi.";
        }
    } else {
        $error = "Semua kolom harus diisi.";
    }
}

// Ambil data reservasi dari database
$query = "SELECT r.id, r.customer_name, r.service_id, r.reservation_date, r.status, s.nama_layanan 
          FROM reservations r 
          JOIN services s ON r.service_id = s.id";
$result = $conn->query($query);

if (!$result) {
    die("Query gagal: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi - Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #d81b60;
            color: white;
            padding: 15px 25px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .navbar a,
        .navbar .btn {
            text-decoration: none;
            color: white; /* Warna teks tombol putih */
            font-weight: bold;
            margin-left: 20px;
        }
        .navbar .btn {
            background-color: transparent; /* Background transparan */
            padding: 10px 15px;
            border-radius: 5px;
        }
        .navbar .btn:hover {
            background-color: #e0e0e0;
        }
        .container {
            margin: 40px auto;
            max-width: 800px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #d81b60;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container {
            margin-bottom: 30px;
        }
        input[type="text"], input[type="date"], select, button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #d81b60;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #c2185b;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #d81b60;
            color: white;
        }
        td img {
            width: 100px;
            height: auto;
        }
        .status {
            padding: 5px 10px;
            color: white;
            border-radius: 5px;
        }
        .status.pending {
            background-color: #ff9800;
        }
        .status.confirmed {
            background-color: #4caf50;
        }
        .status.cancelled {
            background-color: #f44336;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h2 style="flex: 1; text-align: left;">Reservasi - Admin</h2>
        <a href="logout.php" class="btn">Logout</a>
        <a href="admin_dashboard.php" class="btn">Kembali</a>
    </div>

    <!-- Form Reservasi -->
    <div class="container">
        <h1>Tambah Reservasi</h1>

        <!-- Tampilkan pesan sukses atau error -->
        <?php if (isset($success_message)): ?>
            <div style="color: green; text-align: center; margin-bottom: 20px;"><?php echo $success_message; ?></div>
        <?php elseif (isset($error)): ?>
            <div style="color: red; text-align: center; margin-bottom: 20px;"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="post" action="">
                <label for="customer_name">Nama Pelanggan</label>
                <input type="text" id="customer_name" name="customer_name" required>

                <label for="service_id">Pilih Layanan</label>
                <select id="service_id" name="service_id" required>
                    <?php
                    $services_query = "SELECT * FROM services";
                    $services_result = $conn->query($services_query);
                    while ($service = $services_result->fetch_assoc()) {
                        echo "<option value='{$service['id']}'>{$service['nama_layanan']}</option>";
                    }
                    ?>
                </select>

                <label for="reservation_date">Tanggal Reservasi</label>
                <input type="date" id="reservation_date" name="reservation_date" required>

                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="cancelled">Cancelled</option>
                </select>

                <button type="submit">Tambah Reservasi</button>
            </form>
        </div>

        <h1>Daftar Reservasi</h1>

        <!-- Tabel Reservasi -->
        <table>
            <thead>
                <tr>
                    <th>Nama Pelanggan</th>
                    <th>Layanan</th>
                    <th>Tanggal Reservasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status_class = '';
                        switch ($row['status']) {
                            case 'pending':
                                $status_class = 'pending';
                                break;
                            case 'confirmed':
                                $status_class = 'confirmed';
                                break;
                            case 'cancelled':
                                $status_class = 'cancelled';
                                break;
                        }
                        echo "<tr>
                            <td>{$row['customer_name']}</td>
                            <td>{$row['nama_layanan']}</td>
                            <td>{$row['reservation_date']}</td>
                            <td><span class='status $status_class'>{$row['status']}</span></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Tidak ada reservasi tersedia.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
