<?php
session_start();

// Pastikan pengguna sudah login dengan role 'admin'
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require 'db_connect.php';

// Ambil daftar layanan
$result = $conn->query("SELECT * FROM services");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    
    <style>
        /* Animasi Fade-In untuk seluruh halaman */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Animasi untuk tabel */
        @keyframes tableSlideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animasi untuk footer */
        @keyframes footerSlideUp {
            from {
                opacity: 0;
                transform: translateY(100px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Gaya untuk tombol Tambah Layanan */
        .tambah-layanan {
            text-align: left;
            margin-left: 20px;
            margin-top: 20px;
            animation: fadeIn 1s ease-out forwards;
        }
        .tambah-layanan a {
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            background-color: #d81b60; /* Pink tua */
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .tambah-layanan a:hover {
            background-color: #ad1457; /* Pink lebih gelap saat hover */
        }

        /* Gaya umum */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            animation: fadeIn 1s ease-out forwards; /* Animasi fadeIn pada body */
        }

        /* Navbar tanpa animasi slideIn */
        .navbar {
            background-color: #d81b60;
            color: white;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin-left: 20px;
            padding: 10px 15px; /* Menambahkan padding untuk tombol agar lebih terlihat */
            border-radius: 5px; /* Menambahkan border radius agar tombol lebih halus */
            transition: background-color 0.3s ease; /* Menambahkan transisi pada background */
        }

        .navbar a:hover {
            background-color: #ad1457; /* Pink lebih gelap saat hover */
        }

        .container {
            width: 90%;
            margin: 40px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: tableSlideIn 1s ease-out forwards; /* Animasi slide-in pada container */
        }
        h1 {
            text-align: center;
            color: #d81b60;
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #d81b60;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }

        /* Styling untuk tombol Edit dan Hapus pada tabel */
        td a {
            color: white;
            background-color: #d81b60; /* Warna pink untuk tombol */
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin: 0 5px;
            transition: background 0.3s ease;
        }
        td a:hover {
            background-color: #ad1457; /* Pink lebih gelap saat hover */
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            background-color: #d81b60;
            color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: footerSlideUp 1s ease-out forwards; /* Animasi footer muncul dari bawah */
        }

        .footer h3 {
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        .footer p {
            margin: 10px 0;
            font-size: 18px;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .contact-info a {
            text-decoration: none;
            color: white;
            margin: 12px 0;
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 30px;
            background-color: #ad1457;
            transition: background-color 0.3s ease;
        }

        .contact-info a:hover {
            background-color: #c2185b;
        }

        .contact-info a span {
            margin-right: 10px;
        }

        .map-container {
            margin-top: 30px;
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
        }

        .map-container iframe {
            width: 100%;
            height: 400px;
            border: none;
            border-radius: 15px;
        }

        @media (max-width: 768px) {
            .footer {
                padding: 20px;
            }
            .map-container iframe {
                height: 300px;
            }
        }

        /* Gaya untuk tombol Reservasi */
        .navbar .tombol-reservasi {
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            background-color: #d81b60; /* Pink tua */
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s ease;
            margin-left: 20px; /* Memberikan jarak antara tombol */
        }

        .navbar .tombol-reservasi:hover {
            background-color: #ad1457; /* Pink lebih gelap saat hover */
        }

    </style>
</head>
<body>
    <div class="navbar">
        <h2>Dashboard Admin - Klinik Kecantikan</h2>
        <div>
            <a href="profil_admin.php">Profil</a>
            <a href="reservations.php" class="tombol-reservasi">Reservasi</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Tombol Tambah Layanan -->
    <div class="tambah-layanan">
        <a href="add_service.php">Tambah Layanan</a>
    </div>

    <div class="container">
        <h1>Daftar Layanan Klinik Kecantikan</h1>
        
        <!-- Notifikasi -->
        <?php
        if (isset($_GET['success'])) {
            echo "<p style='color: green;'>{$_GET['success']}</p>";
        }
        if (isset($_GET['error'])) {
            echo "<p style='color: red;'>{$_GET['error']}</p>";
        }
        ?>

        <!-- Tabel Layanan -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Layanan</th>
                    <th>Deskripsi</th>
                    <th>Harga</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Menampilkan data layanan dalam tabel
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nama_layanan']}</td>
                            <td>{$row['deskripsi']}</td>
                            <td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
                            <td><img src='images/{$row['gambar']}' alt='Gambar Layanan' width='100'></td>
                            <td>
                                <a href='edit_service.php?id={$row['id']}'>Edit</a> | 
                                <a href='delete_service.php?id={$row['id']}' onclick='return confirm(\"Yakin ingin menghapus layanan ini?\")'>Hapus</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada layanan tersedia.</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </div>

    <!-- Footer with Contact Info and Map -->
    <div class="footer">
        <h3>Kontak Kami</h3>
        <div class="contact-info">
            <p><a href="tel:+6281234567890">📞 0812-3456-7890 (Telepon)</a></p>
            <p><a href="https://wa.me/6281234567890">💬 WhatsApp</a></p>
            <p><a href="https://instagram.com/clinic_instagram">📷 Instagram</a></p>
        </div>
        
        <div class="map-container">
            <h3>Lokasi Kami</h3>
            <!-- Embed Google Map -->
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.078839117178!2d-122.40166168468134!3d37.793797779755164!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085808bdb0f1db1%3A0x210f10096d2b4f9a!2sApple%20Park!5e0!3m2!1sen!2sus!4v1671225555608!5m2!1sen!2sus" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>

</body>
</html>
