<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

require 'db_connect.php';

$user_id = $_SESSION['user_id'];

// Ambil data user untuk ditampilkan
if ($stmt = $conn->prepare("SELECT username, email, profile_picture FROM users WHERE id = ?")) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

// Proses Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $image_path = $user['profile_picture'];  // Defaultnya tetap foto lama

    // Jika foto profil diupload, proses upload gambar
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $upload_dir = 'images/';
        $image_name = basename($_FILES['profile_picture']['name']);
        $target_file = $upload_dir . $image_name;

        // Cek apakah file gambar yang diupload adalah gambar
        $image_type = mime_content_type($_FILES['profile_picture']['tmp_name']);
        if (strpos($image_type, 'image') !== false) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                $image_path = $target_file; // Update image path jika foto baru berhasil di-upload
            } else {
                $error = "Gagal mengupload gambar.";
            }
        } else {
            $error = "Hanya file gambar yang diperbolehkan.";
        }
    }

    // Perbarui data ke database (username, email, dan foto profil)
    $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE id = ?");
    $update_stmt->bind_param("sssi", $username, $email, $image_path, $user_id);

    if ($update_stmt->execute()) {
        $success = "Profil berhasil diperbarui.";
    } else {
        $error = "Gagal memperbarui profil.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        .navbar {
            background-color: #d81b60;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .container {
            width: 60%;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            color: #d81b60;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label, input {
            margin-bottom: 15px;
        }
        input[type="text"], input[type="email"], input[type="file"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #d81b60;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #c2185b;
        }
        .profile-picture {
            display: block;
            margin: 20px auto;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            border: 2px solid #ddd;
        }
        .success-message, .error-message {
            text-align: center;
            font-size: 16px;
            margin-top: 20px;
        }
        .success-message {
            color: green;
        }
        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>Profil User</h2>
        <a href="user_dashboard.php">Kembali</a>
    </div>

    <div class="container">
        <h1>Edit Profil Anda</h1>

        <?php if (isset($success)) echo "<p class='success-message'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

        <!-- Menampilkan foto profil -->
        <div class="profile-picture" style="background-image: url('<?= !empty($user['profile_picture']) ? $user['profile_picture'] : 'images/default.png'; ?>');"></div>

        <form method="POST" action="profil_user.php" enctype="multipart/form-data">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="profile_picture">Foto Profil:</label>
            <input type="file" id="profile_picture" name="profile_picture">

            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>
