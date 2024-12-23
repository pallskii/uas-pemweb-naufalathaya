<?php
session_start();
include '../config/db_config.php';

// Periksa apakah pengguna telah login
if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri Gambar</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <script src="../public/js/gallery.js" defer></script>
</head>

<body>
    <header class="header">
        <div class="container">
            <a href="../index.php" class="logo">Pallski</a>
            <p>Platform untuk menampilkan galeri karya dan cerita kreatif</p>
        </div>
    </header>

    <nav class="navigation">
        <ul>
            <li><a href="public-gallery.php">Jelajahi Galeri</a></li>
            <li><a href="dashboard-gallery.php">Dashboard</a></li>
            <li><a href="../controllers/logout.php" class="logout-button">Logout</a></li>
        </ul>
    </nav>

    <main>
        <section class="hero">
            <h1>Selamat Datang di Dashboard Galeri</h1>
            <p>Kelola gambar yang telah Anda unggah dan bagikan cerita kreatif Anda.</p>

            <section class="user-info" style="padding: 10px 10px; max-width:400px;">
                <h2>Informasi Pengguna</h2>
                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Informasi</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Nama Pengguna</td>
                            <td><?php echo htmlspecialchars($_SESSION['user_name']); ?></td>
                        </tr>
                        <tr>
                            <td>IP Address</td>
                            <td><?php echo htmlspecialchars($_SESSION['ip_address']); ?></td>
                        </tr>
                        <tr>
                            <td>Browser</td>
                            <td><?php echo htmlspecialchars($_SESSION['browser']); ?></td>
                        </tr>
                        <tr>
                            <td>Cookie PHPSESSID</td>
                            <td>
                                <?php
                                echo isset($_COOKIE['PHPSESSID']) ? htmlspecialchars($_COOKIE['PHPSESSID']) : 'Tidak ada cookie PHPSESSID.';
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p>
                    <a href="cookie-local-session.html">Click disini untuk mencoba Session, Cookie, Local</a>
                </p>
            </section>
        </section>

        <section class="dashboard-form">
            <h2>Formulir Unggah Gambar</h2>

            <form id="galleryForm" method="POST" action="../controllers/gallery_controller.php?action=create"
                enctype="multipart/form-data">
                <label>
                    Judul Gambar:
                    <input type="text" name="title" id="title" placeholder="Judul gambar" required>
                </label>
                <label>
                    Deskripsi:
                    <textarea name="description" id="description" placeholder="Deskripsi gambar" required></textarea>
                </label>
                <label>
                    URL Gambar:
                    <input type="text" name="image_url" id="image_url" accept="Link Gambar" required>
                </label>
                <button type="submit" id="submitButton">Unggah Gambar</button>
            </form>
        </section>

        <section class="dashboard-table">
            <h2>Daftar Gambar</h2>
            <table id="galleryTable">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data galeri akan dimuat menggunakan JavaScript -->
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>