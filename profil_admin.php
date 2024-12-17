<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require 'db_connect.php';

// Ambil data admin dari database
$user_id = $_SESSION['user_id']; // Sesuaikan dengan sesi user ID
$stmt = $conn->prepare("SELECT username, email, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin</title>
    <link rel="stylesheet" href="style.css">
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
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            margin-left: 10px;
        }
        .container {
            margin: 50px auto;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #d81b60;
            text-align: center;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center; /* Menambahkan text-align center untuk kolom */
        }
        th {
            background-color: #d81b60;
            color: white;
        }
        .profile-image {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-image img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h2>Profil Admin</h2>
        <div>
            <a href="admin_dashboard.php">Kembali</a> <!-- Menambahkan tombol Kembali -->
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Kontainer Profil -->
    <div class="container">
        <h1>Data Profil Anda</h1>
        
        <!-- Menampilkan Foto Profil -->
        <div class="profile-image">
            <?php if ($user['profile_picture']): ?>
                <img src="images/<?= htmlspecialchars($user['profile_picture']) ?>" alt="Foto Profil">
            <?php else: ?>
                <img src="images/profil1.jpg" alt="Foto Profil Default">
            <?php endif; ?>
        </div>

        <!-- Menampilkan Data Profil dalam Tabel -->
        <table>
            <tr>
                <th>Username</th>
                <td><?= htmlspecialchars($user['username']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($user['email']) ?></td>
            </tr>
        </table>
    </div>
</body>
</html>

