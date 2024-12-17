<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - Klinik Kecantikan</title>
    <style>
        /* Global Style */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #fff0f5; /* Light pink */
            color: #333;
            animation: fadeIn 1.5s ease-out; /* Animasi fadeIn untuk body */
        }

        /* Animasi Fade In */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Header Section */
        .header {
            text-align: center;
            padding: 100px 20px;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), 
                        url('beauty.jpg') no-repeat center center/cover;
            color: white;
            animation: slideDown 1s ease-out; /* Animasi slideDown pada header */
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header h1 {
            font-size: 48px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header p {
            font-size: 18px;
            margin: 10px 0 20px;
        }

        /* Button Style */
        .login-btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #d81b60; /* Pink */
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background 0.3s ease;
            animation: fadeIn 2s ease-out; /* Animasi fadeIn untuk tombol */
        }

        .login-btn:hover {
            background-color: #ad1457; /* Darker pink */
        }

        /* Gallery Section */
        .gallery {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 50px auto;
            max-width: 1200px;
            gap: 20px;
            animation: fadeIn 2s ease-out; /* Animasi fadeIn pada galeri */
        }

        .gallery-item {
            text-align: center;
            max-width: 300px;
            opacity: 0;
            animation: slideUp 1s ease-out forwards; /* Animasi slideUp untuk item galeri */
        }

        /* Animasi slideUp untuk galeri item */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .gallery img {
            width: 100%; /* Lebar gambar menyesuaikan container */
            height: 200px; /* Tinggi gambar seragam */
            object-fit: cover; /* Menjaga proporsi gambar */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .gallery img:hover {
            transform: scale(1.05);
        }

        .gallery-item p {
            margin-top: 10px;
            font-weight: bold;
            color: #d81b60;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #d81b60;
            color: white;
            animation: fadeIn 2s ease-out; /* Animasi fadeIn pada footer */
        }

    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <h1>Selamat Datang di Klinik Kecantikan</h1>
        <p>Tempat terbaik untuk perawatan tubuh, wajah, rambut, gigi, dan kuku Anda</p>
        <a href="login.php" class="login-btn">Masuk</a>
    </div>

    <!-- Gallery Section -->
    <div class="gallery">
        <div class="gallery-item">
            <img src="wajah.jpg" alt="Perawatan Wajah">
            <p>Perawatan Wajah</p>
        </div>
        <div class="gallery-item">
            <img src="kulit.jpg" alt="Perawatan Tubuh">
            <p>Perawatan Tubuh</p>
        </div>
        <div class="gallery-item">
            <img src="rambut.jpg" alt="Perawatan Rambut">
            <p>Perawatan Rambut</p>
        </div>
        <div class="gallery-item">
            <img src="kuku.jpg" alt="Perawatan Kuku">
            <p>Perawatan Kuku</p>
        </div>
        <div class="gallery-item">
            <img src="gigi.jpg" alt="Perawatan Gigi">
            <p>Perawatan Gigi</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; <?php echo date("Y"); ?> Klinik Kecantikan. Semua Hak Dilindungi.</p>
    </div>
</body>
</html>
