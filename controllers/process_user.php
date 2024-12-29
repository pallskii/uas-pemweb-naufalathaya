<?php
session_start();
include '../config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $confirmPassword = htmlspecialchars($_POST['confirm_password']);
    $ip = $_SERVER['REMOTE_ADDR'];
    $browser = $_SERVER['HTTP_USER_AGENT'];

    // Jika IPv6 localhost (::1), ganti dengan IPv4 localhost (127.0.0.1)
    if ($ip === '::1') {
        $ip = '127.0.0.1';
    }

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email tidak valid.");
    }

    // Validasi password
    if ($password !== $confirmPassword) {
        die("Password dan konfirmasi password tidak cocok.");
    }

    // Periksa apakah email sudah terdaftar
    $checkEmailStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        die("Email sudah terdaftar.");
    }
    $checkEmailStmt->close();

    // Generate UUID untuk ID unik
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

    $id = generateUuid();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Simpan data pengguna ke database
    $stmt = $conn->prepare("INSERT INTO users (id, name, email, password, ip_address, browser) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $id, $username, $email, $hashedPassword, $ip, $browser);

    if ($stmt->execute()) {
        $_SESSION['username'] = $username;
        http_response_code(200); // Status 200 untuk sukses
        echo "Data berhasil disimpan.";
        exit;
    } else {
        http_response_code(400); // Status 400 untuk error
        echo "Gagal menyimpan data: " . $stmt->error;
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Untuk mendapatkan daftar pengguna (opsional, jika diperlukan)
    $query = "SELECT name, email FROM users";
    $result = $conn->query($query);

    $users = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($users);
}

$conn->close();
?>
