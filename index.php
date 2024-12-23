<?php
session_start();
include './config/db_config.php';

// Ambil data galeri dari database
$query = "SELECT gallery_showcases.title, gallery_showcases.description, gallery_showcases.image_url, users.name AS uploaded_by 
          FROM gallery_showcases 
          INNER JOIN users ON gallery_showcases.user_id = users.id 
          ORDER BY gallery_showcases.created_at DESC";
$result = $conn->query($query);

// Periksa apakah ada data yang ditemukan
$galleryItems = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $galleryItems[] = $row;
    }
    // Ambil hanya 3 item pertama
    $galleryItems = array_slice($galleryItems, 0, 3);
} else {
    $galleryItems = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pallski - Gallery Showcase</title>
    <link rel="stylesheet" href="./public/css/style.css" />
</head>

<body>
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">Pallski</a>
            <p>Platform untuk menampilkan galeri karya dan cerita kreatif</p>
        </div>
    </header>

    <nav class="navigation">
        <ul>
            <li><a href="./views/public-gallery.php">Jelajahi Galeri</a></li>
            <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']): ?>
                <li><a href="./views/dashboard-gallery.php">Dashboard</a></li>
                <li><a href="./controllers/logout.php" class="logout-button">Logout</a></li>
            <?php else: ?>
                <li><a href="./views/register.php">Daftar</a></li>
                <li><a href="./views/login.php">Masuk</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main class="main-content">
        <section class="hero">
            <h1>Selamat Datang di Pallski</h1>
            <p>Ungkapkan kreativitas Anda melalui galeri visual yang memikat</p>
            <a href="./views/dashboard-gallery.php" class="cta-button">Unggah Karya</a>
        </section>

        <section class="features">
            <h2>Kenapa Memilih Pallski?</h2>

            <ul>
                <li>Bagikan karya visual yang menginspirasi dunia.</li>
                <li>Jelajahi galeri penuh kreativitas dari berbagai penjuru.</li>
                <li>Temukan cerita mendalam di balik setiap gambar.</li>
            </ul>
        </section>

        <h2 style="text-align:center;">Galeri Terkini</h2>

        <section class="gallery">
            <?php if (!empty($galleryItems)): ?>
                <?php foreach ($galleryItems as $item): ?>
                    <div class="gallery-item">
                        <h3>Judul: <?php echo htmlspecialchars($item['title']); ?></h3>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <p><strong>Diunggah oleh:</strong> <?php echo htmlspecialchars($item['uploaded_by']); ?></p>
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($item['title']); ?>" />
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada karya di galeri saat ini. Jadilah yang pertama menambahkan karya Anda!</p>
            <?php endif; ?>
        </section>

        <section class="cta">
            <h2>Siap Menambahkan Karya Anda?</h2>

            <p>Bagikan cerita dan karya visual Anda sekarang!</p>

            <a href="./views/register.php" class="cta-button">Mulai Sekarang</a>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; Copyright Pallski 2024</p>
    </footer>
</body>

</html>