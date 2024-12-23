### Pallski Showcase Gallery

Pallski adalah platform inovatif yang dirancang untuk memfasilitasi pengguna dalam berbagi karya visual mereka melalui galeri digital. Platform ini mengintegrasikan teknologi client-side dan server-side untuk memastikan pengalaman pengguna yang optimal, serta mendukung pengelolaan data melalui integrasi database yang aman.

**Link Hosting**:

- HTTP: [pallskii.freesite.online](http://pallskii.freesite.online)
- HTTPS: [pallskii.freesite.online](https://pallskii.freesite.online)

---

### **Bagian 1: Pemrograman Client-side (30%)**

#### 1.1 **Manipulasi DOM dengan JavaScript (15%)**

**Kriteria:**

- Form dengan minimal 4 elemen input untuk pengguna.
- Data dari server ditampilkan dalam tabel HTML.

**Penerapan dalam Studi Kasus:**

- **Form Input**: Form untuk menambahkan galeri terdapat pada file `views/dashboard-gallery.php`:

```html
<form
  id="galleryForm"
  method="POST"
  action="../controllers/gallery_controller.php?action=create"
>
  <label>
    Judul:
    <input
      type="text"
      name="title"
      id="title"
      placeholder="Judul karya"
      required
    />
  </label>
  <label>
    Deskripsi:
    <textarea
      name="description"
      id="description"
      placeholder="Deskripsi karya"
    ></textarea>
  </label>
  <label>
    URL Gambar:
    <input
      type="text"
      name="image_url"
      id="image_url"
      placeholder="Tautan gambar"
      required
    />
  </label>
</form>
```

- **Tabel HTML**: Data dari server ditampilkan menggunakan JavaScript ke dalam tabel berikut:

```html
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
    <!-- Data galeri dimuat oleh JavaScript -->
  </tbody>
</table>
```

- **Manipulasi DOM**: Data galeri dimuat dan ditampilkan dengan fungsi JavaScript di file `public/js/gallery.js`:

```javascript
function loadGallery() {
  fetch("../controllers/gallery_controller.php?action=fetch", { method: "GET" })
    .then((response) => response.json())
    .then((gallery) => {
      resetTable();
      gallery.forEach((item) => addGalleryRow(item));
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Gagal memuat data galeri.");
    });
}
```

---

#### 1.2 **Event Handling (15%)**

**Kriteria:**

- Implementasi minimal 3 event handler untuk form.
- Validasi input sebelum dikirimkan ke server.

**Penerapan dalam Studi Kasus:**

- **Event Handling**: File `public/js/gallery.js` mencakup berbagai event untuk form dan tabel:

  1. **Submit Form**: Mengirim data form ke server untuk disimpan:

     ```javascript
     galleryForm.addEventListener("submit", function (e) {
       e.preventDefault();
       const formData = new URLSearchParams({
         title: titleInput.value.trim(),
         description: descriptionInput.value.trim(),
         image_url: imageUrlInput.value.trim(),
       });
       fetch(`../controllers/gallery_controller.php?action=create`, {
         method: "POST",
         body: formData,
       })
         .then((response) => response.json())
         .then((data) => {
           alert(data.message || data.error);
           resetTable();
           loadGallery();
           galleryForm.reset();
         })
         .catch((error) => {
           console.error("Error:", error);
           alert("Gagal menambahkan galeri.");
         });
     });
     ```

  2. **Edit Data**: Mengisi form dengan data yang diambil dari server untuk diedit:

     ```javascript
     editBtn.addEventListener("click", function () {
       const id = this.getAttribute("data-id");
       fetch(`../controllers/gallery_controller.php?action=fetchById&id=${id}`)
         .then((response) => response.json())
         .then((data) => {
           if (data) {
             titleInput.value = data.title;
             descriptionInput.value = data.description;
             imageUrlInput.value = data.image_url;
           } else {
             alert("Data tidak ditemukan.");
           }
         });
     });
     ```

  3. **Validasi Form**: Validasi input untuk memastikan data valid sebelum dikirimkan:
     ```javascript
     function validateForm() {
       const isValid = titleInput.value.trim() && imageUrlInput.value.trim();
       submitButton.disabled = !isValid;
     }
     titleInput.addEventListener("input", validateForm);
     descriptionInput.addEventListener("input", validateForm);
     imageUrlInput.addEventListener("input", validateForm);
     ```

Dengan penerapan tersebut, fitur manipulasi DOM dan event handling dalam **Pallski** telah memenuhi kriteria penilaian studi kasus.

---

### **Bagian 2: Pemrograman Server-side (30%)**

#### 2.1 **Pengelolaan Data dengan PHP (20%)**

**Kriteria:**

- Menggunakan metode **POST/GET** pada formulir untuk mengelola data.
- Melakukan validasi data dari variabel global (`$_POST`, `$_GET`) untuk keamanan.
- Menyimpan data ke basis data, termasuk **alamat IP** dan **jenis browser** pengguna.

**Penerapan dalam Studi Kasus Pallski:**

- **Metode POST pada Formulir**: Pada file `controllers/gallery_controller.php`, metode POST digunakan untuk menerima data dari form pengguna dan menyimpannya ke basis data.

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $id = generateUuid(); // Hasilkan UUID unik
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
```

- **Validasi Data Server-side**: Data yang diterima dari variabel `$_POST` divalidasi menggunakan fungsi `htmlspecialchars()` untuk mencegah serangan injeksi seperti XSS.

```php
$title = htmlspecialchars($_POST['title']);
$description = htmlspecialchars($_POST['description']);
$image_url = htmlspecialchars($_POST['image_url']);
```

- **Penyimpanan Data ke Database**: Data seperti judul, deskripsi, dan URL gambar disimpan dalam tabel `gallery_showcases`.
- **Jenis Browser dan Alamat IP Pengguna**: Pada file `controllers/process_user.php`, data alamat IP dan jenis browser pengguna disimpan saat pengguna mendaftar.

```php
$ip = $_SERVER['REMOTE_ADDR'];
$browser = $_SERVER['HTTP_USER_AGENT'];

// Jika IPv6 localhost (::1), ubah menjadi IPv4 localhost (127.0.0.1)
if ($ip === '::1') {
    $ip = 'IP Local: 127.0.0.1';
}

$stmt = $conn->prepare("INSERT INTO users (name, email, password, ip_address, browser) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $email, $hashedPassword, $ip, $browser);
```

---

#### 2.2 **Objek PHP Berbasis OOP (10%)**

**Kriteria:**

- Membuat kelas PHP berbasis **OOP** dengan minimal dua metode.
- Menggunakan objek kelas tersebut untuk kebutuhan tertentu.

**Penerapan dalam Studi Kasus Pallski:**

- **Objek PHP dengan Dua Metode**: Pada file `models/User.php`, terdapat kelas `User` dengan dua metode utama:
  - `findByEmail()` untuk mencari pengguna berdasarkan email.
  - `verifyPassword()` untuk memverifikasi kecocokan password dengan hash.

```php
class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Gagal mempersiapkan query: " . $this->conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null; // Jika tidak ditemukan
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        return $user;
    }

    public function verifyPassword($inputPassword, $storedHash)
    {
        return password_verify($inputPassword, $storedHash);
    }
}
```

- **Penggunaan Objek Kelas**: Objek `User` digunakan untuk login pengguna di file `controllers/user_controller.php`:

```php
$userModel = new User($conn);
$user = $userModel->findByEmail($email);

if ($user && $userModel->verifyPassword($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['ip_address'] = $user['ip_address'];
    $_SESSION['browser'] = $user['browser'];
    $_SESSION['is_logged_in'] = true;

    echo json_encode(["success" => "Login berhasil.", "redirect" => "dashboard-gallery.php"]);
} else {
    echo json_encode(["error" => "Email atau password salah."]);
}
```

---

Dengan implementasi di atas, sistem berhasil memproses data dari formulir, memastikan validasi yang aman, menyimpan data ke basis data, serta memanfaatkan konsep OOP untuk pengelolaan pengguna.

---

### **Bagian 3: Manajemen Basis Data (20%)**

#### 3.1 **Pembuatan Struktur Tabel Database (5%)**

**Kriteria:**

- Menyusun tabel database yang sesuai dengan kebutuhan aplikasi, termasuk hubungan antar-tabel.

**Penerapan dalam Studi Kasus Pallski:**

Pada file `config/table-gallery-showcase.sql`, tabel `users` dan `gallery_showcases` dirancang untuk menyimpan data pengguna dan karya galeri.

```sql
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    browser VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE gallery_showcases (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    image_url VARCHAR(2083) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
```

- **Tabel `users`** digunakan untuk mencatat informasi pengguna, termasuk alamat IP dan browser.
- **Tabel `gallery_showcases`** menyimpan data karya galeri, termasuk referensi ke pengguna melalui `user_id`.

---

#### 3.2 **Konfigurasi Koneksi Basis Data (5%)**

**Kriteria:**

- Membuat file konfigurasi untuk menghubungkan aplikasi dengan basis data.

**Penerapan dalam Studi Kasus Pallski:**

File `config/db_config.php` menangani koneksi ke database dan memastikan koneksi berhasil dilakukan.

```php
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pemweb-uas-pallski";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
```

- **Koneksi database** menggunakan variabel server, username, password, dan nama basis data.
- **Validasi koneksi**: Jika koneksi gagal, pesan kesalahan akan ditampilkan.

---

#### 3.3 **Operasi Manipulasi Data (CRUD) (10%)**

**Kriteria:**

- Menerapkan operasi CRUD (Create, Read, Update, Delete) pada tabel database.

**Penerapan dalam Studi Kasus Pallski:**

Operasi CRUD diimplementasikan dalam `controllers/gallery_controller.php` untuk tabel `gallery_showcases`.

1. **Create (Menyimpan Data Baru)**:

   Data baru ditambahkan ke tabel `gallery_showcases` dengan validasi input untuk mencegah serangan XSS.

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
       $id = generateUuid();
       $title = htmlspecialchars($_POST['title']);
       $description = htmlspecialchars($_POST['description']);
       $image_url = htmlspecialchars($_POST['image_url']);

       $stmt = $conn->prepare("INSERT INTO gallery_showcases (id, title, description, image_url) VALUES (?, ?, ?, ?)");
       $stmt->bind_param("ssss", $id, $title, $description, $image_url);

       if ($stmt->execute()) {
           echo json_encode(["message" => "Karya berhasil ditambahkan!", "id" => $id]);
       } else {
           echo json_encode(["error" => "Gagal menambahkan karya."]);
       }
       $stmt->close();
       exit;
   }
   ```

2. **Read (Membaca Data)**:

   - **Semua data**:

     ```php
     if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetch') {
         $query = "SELECT * FROM gallery_showcases";
         $result = $conn->query($query);
         $galleryItems = [];
         while ($row = $result->fetch_assoc()) {
             $galleryItems[] = $row;
         }
         echo json_encode($galleryItems);
         exit;
     }
     ```

   - **Data spesifik berdasarkan ID**:

     ```php
     if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetchById') {
         $id = htmlspecialchars($_GET['id']);
         $stmt = $conn->prepare("SELECT * FROM gallery_showcases WHERE id = ?");
         $stmt->bind_param("s", $id);
         $stmt->execute();
         $result = $stmt->get_result();

         if ($result->num_rows > 0) {
             echo json_encode($result->fetch_assoc());
         } else {
             echo json_encode(["error" => "Karya tidak ditemukan."]);
         }
         $stmt->close();
         exit;
     }
     ```

3. **Update (Memperbarui Data)**:

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
       $id = htmlspecialchars($_POST['id']);
       $title = htmlspecialchars($_POST['title']);
       $description = htmlspecialchars($_POST['description']);
       $image_url = htmlspecialchars($_POST['image_url']);

       $stmt = $conn->prepare("UPDATE gallery_showcases SET title = ?, description = ?, image_url = ? WHERE id = ?");
       $stmt->bind_param("ssss", $title, $description, $image_url, $id);

       if ($stmt->execute()) {
           echo json_encode(["message" => "Karya berhasil diperbarui!"]);
       } else {
           echo json_encode(["error" => "Gagal memperbarui karya."]);
       }
       $stmt->close();
       exit;
   }
   ```

4. **Delete (Menghapus Data)**:

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
       $id = htmlspecialchars($_POST['id']);
       $stmt = $conn->prepare("DELETE FROM gallery_showcases WHERE id = ?");
       $stmt->bind_param("s", $id);

       if ($stmt->execute()) {
           echo json_encode(["message" => "Karya berhasil dihapus!"]);
       } else {
           echo json_encode(["error" => "Gagal menghapus karya."]);
       }
       $stmt->close();
       exit;
   }
   ```

Kode di atas mencakup seluruh aspek manajemen basis data, mulai dari perancangan tabel hingga operasi CRUD

---

### **Bagian 4: Manajemen State (20%)**

#### 4.1 **Manajemen State dengan Session (10%)**

**Kriteria:**

- Menggunakan `session_start()` untuk memulai session.
- Menyimpan informasi pengguna ke dalam session untuk kebutuhan aplikasi.

**Penerapan dalam Studi Kasus Pallski:**

1. **Memulai Session**  
   Pada file seperti `session/session.php` dan `views/dashboard-gallery.php`, session dimulai dengan `session_start()` untuk memastikan akses data session.

   ```php
   <?php
   session_start();
   if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
       header("Location: ../views/login.php");
       exit;
   }

   $userName = $_SESSION['user_name'];
   ?>
   ```

2. **Menyimpan Data dalam Session**  
   Pada file `controllers/user_controller.php`, setelah login berhasil, data pengguna seperti nama, IP, dan browser disimpan ke dalam session.

   ```php
   $_SESSION['user_id'] = $user['id'];
   $_SESSION['user_name'] = $user['name'];
   $_SESSION['ip_address'] = $user['ip_address'];
   $_SESSION['browser'] = $user['browser'];
   $_SESSION['is_logged_in'] = true;
   ```

3. **Menggunakan Data Session**  
   Di halaman dashboard (`views/dashboard-gallery.php`), data session digunakan untuk menampilkan informasi pengguna secara dinamis:

   ```php
   <p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!</p>
   <p>IP Address: <strong><?php echo htmlspecialchars($_SESSION['ip_address']); ?></strong></p>
   <p>Browser: <strong><?php echo htmlspecialchars($_SESSION['browser']); ?></strong></p>
   ```

---

#### 4.2 **Manajemen State dengan Cookie dan Browser Storage (10%)**

**Kriteria:**

- Implementasikan fungsi untuk menetapkan, membaca, dan menghapus cookie.
- Gunakan `localStorage` dan `sessionStorage` untuk menyimpan data di browser.

**Penerapan dalam Studi Kasus Pallski:**

1. **Pengelolaan Cookie**  
   Pada file `views/cookie-local-session.html`, cookie dapat diatur dan dihapus melalui fungsi JavaScript berikut:

   ```javascript
   // Menetapkan Cookie
   document.getElementById("setCookie").addEventListener("click", () => {
     const value = cookieInput.value.trim();
     if (!value) {
       alert("Masukkan nilai terlebih dahulu!");
       return;
     }
     document.cookie = `PallskiCookie=${value}; path=/; max-age=3600`; // Berlaku 1 jam
     updateDisplays();
   });

   // Menghapus Cookie
   document.getElementById("deleteCookie").addEventListener("click", () => {
     document.cookie = "PallskiCookie=; path=/; max-age=0"; // Hapus cookie
     updateDisplays();
   });

   // Memperbarui Tampilan Cookie
   function updateDisplays() {
     const cookies = document.cookie.split("; ").reduce((acc, cookie) => {
       const [key, value] = cookie.split("=");
       acc[key] = value;
       return acc;
     }, {});
     cookieDisplay.textContent = cookies.PallskiCookie || "Belum ada nilai";
   }
   ```

2. **Pengelolaan LocalStorage dan SessionStorage**  
   Informasi tambahan dapat disimpan di browser melalui `localStorage` dan `sessionStorage`:

   ```javascript
   // LocalStorage
   document.getElementById("setLocalStorage").addEventListener("click", () => {
     const value = localStorageInput.value.trim();
     if (!value) {
       alert("Masukkan nilai terlebih dahulu!");
       return;
     }
     localStorage.setItem("PallskiLocalStorage", value);
     updateDisplays();
   });

   document
     .getElementById("deleteLocalStorage")
     .addEventListener("click", () => {
       localStorage.removeItem("PallskiLocalStorage");
       updateDisplays();
     });

   // SessionStorage
   document
     .getElementById("setSessionStorage")
     .addEventListener("click", () => {
       const value = sessionStorageInput.value.trim();
       if (!value) {
         alert("Masukkan nilai terlebih dahulu!");
         return;
       }
       sessionStorage.setItem("PallskiSessionStorage", value);
       updateDisplays();
     });

   document
     .getElementById("deleteSessionStorage")
     .addEventListener("click", () => {
       sessionStorage.removeItem("PallskiSessionStorage");
       updateDisplays();
     });
   ```

3. **Form untuk Pengelolaan State**  
   Halaman `cookie-local-session.html` menyediakan form untuk memanipulasi cookie, `localStorage`, dan `sessionStorage`:

   ```html
   <!-- Form untuk Cookie -->
   <form id="cookieForm">
     <label for="cookieInput">Masukkan Nilai Cookie:</label>
     <input type="text" id="cookieInput" required />
     <button type="button" id="setCookie">Set Cookie</button>
     <button type="button" id="deleteCookie">Hapus Cookie</button>
   </form>
   <p>Cookie saat ini: <span id="cookieDisplay">Belum ada nilai</span></p>

   <!-- Form untuk LocalStorage -->
   <form id="localStorageForm">
     <label for="localStorageInput">Masukkan Nilai LocalStorage:</label>
     <input type="text" id="localStorageInput" required />
     <button type="button" id="setLocalStorage">Set LocalStorage</button>
     <button type="button" id="deleteLocalStorage">Hapus LocalStorage</button>
   </form>
   <p>
     LocalStorage saat ini:
     <span id="localStorageDisplay">Belum ada nilai</span>
   </p>

   <!-- Form untuk SessionStorage -->
   <form id="sessionStorageForm">
     <label for="sessionStorageInput">Masukkan Nilai SessionStorage:</label>
     <input type="text" id="sessionStorageInput" required />
     <button type="button" id="setSessionStorage">Set SessionStorage</button>
     <button type="button" id="deleteSessionStorage">
       Hapus SessionStorage
     </button>
   </form>
   <p>
     SessionStorage saat ini:
     <span id="sessionStorageDisplay">Belum ada nilai</span>
   </p>
   ```

Kode di atas menunjukkan implementasi lengkap manajemen state menggunakan session, cookie, localStorage, dan sessionStorage.

---

### **Bagian Bonus: Panduan Hosting Aplikasi Web (20%)**

#### **Panduan Hosting Aplikasi - Pallski Gallery**

**Pallski Gallery** adalah platform berbasis PHP dan MySQL yang dirancang untuk menampilkan karya visual dan cerita kreatif. Berikut langkah-langkah untuk meng-host aplikasi ini menggunakan **InfinityFree**, termasuk konfigurasi, keamanan, dan alasan memilih platform ini.

---

### **Langkah-langkah Hosting di InfinityFree**

1. **Persiapkan Berkas Aplikasi**

   - Pastikan seluruh file aplikasi, seperti file PHP, CSS, JavaScript, dan database SQL, telah lengkap dan siap diunggah.

2. **Login ke InfinityFree**

   - Masuk ke akun **InfinityFree** melalui [https://infinityfree.net/](https://infinityfree.net/).

3. **Buat Database MySQL**

   - Akses **Control Panel**, pilih **MySQL Databases**, lalu buat database baru. Catat nama database, username, password, dan host yang disediakan.

4. **Konfigurasi Koneksi Database**

   - Edit file `config/db_config.php` agar sesuai dengan informasi dari InfinityFree:
     ```php
     <?php
     $servername = "sqlXXX.infinityfree.com"; // Host database
     $username = "epiz_XXXXXXXX";            // Username dari InfinityFree
     $password = "password_anda";           // Password yang diberikan
     $dbname = "epiz_XXXXXXXX_database";    // Nama database
     $conn = new mysqli($servername, $username, $password, $dbname);
     if ($conn->connect_error) {
         die("Koneksi gagal: " . $conn->connect_error);
     }
     ?>
     ```

5. **Unggah File Aplikasi**

   - Gunakan **File Manager** di InfinityFree atau software FTP seperti **FileZilla**. Upload semua file ke folder **htdocs**, dengan file `index.php` berada di direktori root.

6. **Tes Aplikasi**
   - Akses URL hosting Anda (contoh: `http://yourapp.infinityfreeapp.com`) untuk memastikan aplikasi berjalan dengan baik.

---

### **Mengapa Memilih InfinityFree?**

**InfinityFree** adalah pilihan ideal untuk hosting aplikasi **Pallski Gallery** karena:

- **Gratis dan Mudah Digunakan**  
  Cocok untuk pengembangan awal tanpa biaya.
- **Dukungan PHP dan MySQL**  
  Sepenuhnya kompatibel dengan teknologi yang digunakan pada aplikasi.

- **Ruang Penyimpanan Tak Terbatas**  
  Memungkinkan penyimpanan file tanpa batasan kuota.

- **Custom Domain**  
  Mendukung domain kustom maupun domain gratis dari InfinityFree.

---

### **Keamanan Aplikasi di InfinityFree**

1. **Password Database yang Aman**

   - InfinityFree menyediakan password acak untuk database, memastikan tingkat keamanan yang tinggi.

2. **HTTPS Gratis**

   - Mendukung HTTPS menggunakan sertifikat SSL gratis, melindungi data antara pengguna dan server.

3. **Isolasi Akun Hosting**
   - Setiap akun terisolasi untuk mencegah akses tidak sah antar aplikasi yang di-host di server yang sama.

---

### **Tips Konfigurasi Server**

1. **Pengaturan Database**

   - Perbarui `db_config.php` dengan informasi database dari InfinityFree.

2. **Penempatan File `index.php`**

   - Pastikan file utama `index.php` ada di root folder **htdocs** untuk akses langsung.

3. **Uji Fungsi Aplikasi**
   - Cek fungsi CRUD (Create, Read, Update, Delete) untuk memastikan semua fitur aplikasi berjalan tanpa masalah. Gunakan **phpMyAdmin** untuk verifikasi database.

Panduan ini memastikan aplikasi **Pallski Gallery** Anda dapat di-host dengan lancar di InfinityFree, dengan konfigurasi yang optimal dan aman.
