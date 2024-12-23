<?php
include '../config/db_config.php';

header('Content-Type: application/json');

// Ambil parameter 'action'
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Ambil semua galeri
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetch') {
    $query = "SELECT * FROM gallery_showcases";
    $result = $conn->query($query);
    $gallery = [];
    while ($row = $result->fetch_assoc()) {
        $gallery[] = $row;
    }
    echo json_encode($gallery);
    exit;
}

// Ambil galeri berdasarkan ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetchById') {
    $id = htmlspecialchars($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM gallery_showcases WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Gambar tidak ditemukan."]);
    }
    $stmt->close();
    exit;
}

// Tambahkan galeri baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $id = generateUuid(); // Hasilkan UUID
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $image_url = htmlspecialchars($_POST['image_url']);

    $stmt = $conn->prepare("INSERT INTO gallery_showcases (id, title, description, image_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $id, $title, $description, $image_url);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Gambar berhasil ditambahkan!", "id" => $id]);
    } else {
        echo json_encode(["error" => "Gagal menambahkan gambar."]);
    }
    $stmt->close();
    exit;
}

// Perbarui galeri
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    $id = htmlspecialchars($_POST['id']);
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $image_url = htmlspecialchars($_POST['image_url']);

    $stmt = $conn->prepare("UPDATE gallery_showcases SET title = ?, description = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("ssss", $title, $description, $image_url, $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Gambar berhasil diperbarui!"]);
    } else {
        echo json_encode(["error" => "Gagal memperbarui gambar."]);
    }
    $stmt->close();
    exit;
}

// Hapus galeri
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    $id = htmlspecialchars($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM gallery_showcases WHERE id = ?");
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Gambar berhasil dihapus!"]);
    } else {
        echo json_encode(["error" => "Gagal menghapus gambar."]);
    }
    $stmt->close();
    exit;
}

// Jika 'action' tidak valid
http_response_code(400);
echo json_encode(["error" => "Parameter 'action' tidak valid."]);
$conn->close();

// Fungsi untuk menghasilkan UUID
function generateUuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
