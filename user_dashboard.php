<?php
session_start();

// Pastikan pengguna sudah login dengan role 'user'
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'user') {
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
    <title>Dashboard User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
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
        }
        .container {
            width: 90%;
            margin: 40px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

        /* Gambar di dalam tabel Layanan */
        .service-image {
            max-width: 100px;  /* Lebar gambar maksimal 100px */
            height: auto;      /* Menjaga rasio gambar */
            object-fit: contain; /* Memastikan gambar tidak terdistorsi */
            border-radius: 5px;  /* Memberi sedikit radius pada sudut gambar */
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
            height: 200px;
            border: none;
            border-radius: 15px;
        }

        @media (max-width: 200px) {
            .footer {
                padding: 20px;
            }
            .map-container iframe {
                height: 100px;
            }
        }

    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h2>Dashboard User</h2>
        <div>
            <a href="profil_user.php">Profil</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h1>Daftar Layanan</h1>
        <table>
            <thead>
                <tr>
                    <th>Nama Layanan</th>
                    <th>Deskripsi</th>
                    <th>Harga</th>
                    <th>Gambar</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_layanan']) ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                            <td><img src="images/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_layanan']) ?>" class="service-image"></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Tidak ada layanan yang tersedia.</td>
                    </tr>
                <?php endif; ?>
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
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.078839117178!2d-122.40166168468134!3d37.793797779755164!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085808bdb0f1db1%3A0x210f10096d2b4f9a!2sApple%20Park!5e0!3m2!1sen!2sus!4v1671225555608!5m2!1sen!2sus" allowfullscreen="" loading="lazy" style="height: 400px; width: 100%; border: none; border-radius: 15px;"></iframe>
    </div>

</body>
</html>
