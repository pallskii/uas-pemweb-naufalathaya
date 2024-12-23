<?php
// Koneksi ke database
include '../config/db_config.php';

session_start();

// Inisialisasi variabel pencarian
$searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Query untuk mengambil data galeri beserta pengguna yang mengunggahnya
if (!empty($searchTerm)) {
    $stmt = $conn->prepare(
        "SELECT gallery_showcases.title, gallery_showcases.description, gallery_showcases.image_url, users.name AS uploader_name 
        FROM gallery_showcases 
        INNER JOIN users ON gallery_showcases.user_id = users.id 
        WHERE gallery_showcases.title LIKE ?"
    );
    $searchWildcard = '%' . $searchTerm . '%';
    $stmt->bind_param("s", $searchWildcard);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT gallery_showcases.title, gallery_showcases.description, gallery_showcases.image_url, users.name AS uploader_name 
              FROM gallery_showcases 
              INNER JOIN users ON gallery_showcases.user_id = users.id";
    $result = $conn->query($query);
}

// Periksa apakah data ditemukan
$galleryItems = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $galleryItems[] = $row;
    }
}

// Tutup koneksi database
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelajahi Galeri - Pallski</title>
    <link rel="stylesheet" href="../public/css/style.css">
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
            <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']): ?>
                <li><a href="dashboard-gallery.php">Dashboard</a></li>
                <li><a href="../controllers/logout.php" class="logout-button">Logout</a></li>
            <?php else: ?>
                <li><a href="register.php">Daftar</a></li>
                <li><a href="login.php">Masuk</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="main-content">
        <section class="hero">
            <h1>Jelajahi Galeri</h1>
            <form method="GET" action="public-gallery.php" class="search-form">
                <input type="text" name="search" placeholder="Cari berdasarkan judul gambar..."
                    value="<?php echo $searchTerm; ?>" />
                <button type="submit" class="cta-button">Cari</button>
            </form>
        </section>

        <section class="gallery">
            <?php if (!empty($galleryItems)): ?>
                <?php foreach ($galleryItems as $item): ?>
                    <div class="gallery-item">
                        <h2>Judul: <?php echo htmlspecialchars($item['title']); ?></h2>
                        <p>Deskripsi: <?php echo htmlspecialchars($item['description']); ?></p>
                        <p>Diunggah oleh: <?php echo htmlspecialchars($item['uploader_name']); ?></p>
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($item['title']); ?>" />
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada gambar ditemukan di galeri.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; Copyright Pallski 2024</p>
    </footer>
</body>

</html>