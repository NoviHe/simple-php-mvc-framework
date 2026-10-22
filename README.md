# Simple PHP MVC Framework

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://www.php.net/)
[![Architecture](https://img.shields.io/badge/Architecture-MVC-green.svg)]()
[![Security](https://img.shields.io/badge/Security-PDO%20%26%20CSRF%20Protected-orange.svg)]()
[![License](https://img.shields.io/badge/License-MIT-purple.svg)]()

**Simple PHP MVC Framework** adalah kerangka kerja PHP murni (vanilla) yang minimalis, modern, cepat, dan aman. Framework ini dirancang dengan pola *Model-View-Controller (MVC)* dan *Front Controller*, tanpa dependensi eksternal yang rumit sehingga sangat mudah dipahami, dipelajari, dan dikembangkan untuk aplikasi web maupun REST API.

---

## 🌟 Fitur Utama

- **Pola Arsitektur MVC & Front Controller**: Pemisahan yang rapi dan terstruktur antara logika bisnis (Model), antarmuka pengguna (View), dan alur aplikasi (Controller).
- **Database Layer Modern (PDO)**:
  - Menggunakan ekstensi **PDO (PHP Data Objects)**.
  - Semua operasi query dasar (`all`, `find`, `where`, `create`, `update`, `delete`, `count`) menggunakan **Prepared Statements** dan parameter binding yang **100% kebal terhadap SQL Injection**.
  - Koneksi database ditangani secara *graceful* (aplikasi tidak crash meskipun basis data belum dikonfigurasi).
- **Keamanan Terpadu (Built-in Security)**:
  - **CSRF Protection**: Token anti-pemalsuan permintaan lintas situs (`csrf_token()`, `csrf_field()`, `csrf_verify()`).
  - **XSS Mitigation**: Helper global `e($value)` untuk sanitasi output HTML aman berbasis UTF-8.
  - **Password Hashing**: Menggunakan standar industri `password_hash()` dan `password_verify()` (Bcrypt/Argon2).
  - **Session Hardening**: Konfigurasi otomatis sesi HTTP-only, SameSite, dan pencegahan *Session Fixation*.
- **Konfigurasi Berbasis Lingkungan (`.env`)**:
  - Dilengkapi parser file `.env` mandiri yang ringan tanpa perlu Composer.
  - Pengaturan sensitif (kredensial database, URL, mode debug) terpisah dari kode sumber.
- **Routing Otomatis**:
  - Routing dinamis otomatis memetakan format URL `/{controller}/{action}/{parameter}` ke kelas controller dan metode yang sesuai.
- **Dukungan REST API**:
  - Mengembalikan respons JSON secara instan dan rapi menggunakan `$this->json($data)`.
- **Layout & Template Engine Sederhana**:
  - Mendukung layout master yang dapat digunakan kembali dengan antarmuka yang bersih dan responsif.

---

## 📋 Persyaratan Sistem

- **PHP**: Versi 8.0 atau yang lebih tinggi (Sangat direkomendasikan PHP 8.2+)
- **Ekstensi PHP**:
  - `pdo` & `pdo_mysql` (atau driver database PDO lainnya)
  - `mbstring`
  - `openssl`
  - `session`
  - `json`
- **Web Server**: Apache dengan `mod_rewrite` aktif, Nginx, atau PHP Built-in Server

---

## 🚀 Panduan Memulai Cepat (Quick Start)

### 1. Salin Konfigurasi Lingkungan
Duplikat berkas `.env.example` menjadi `.env`:

```bash
# Windows PowerShell
Copy-Item .env.example .env

# Linux / macOS / Bash
cp .env.example .env
```

Sesuaikan konfigurasi pada berkas `.env` (misalnya pengaturan database MySQL jika diperlukan):

```env
APP_NAME="Simple PHP MVC Framework"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000
TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mvc_db
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
```

### 2. Jalankan Server Lokal

Anda dapat langsung menjalankan framework menggunakan server bawaan PHP:

```bash
php -S localhost:8000 -t public
```

Buka browser dan akses: [http://localhost:8000](http://localhost:8000)

> **Catatan Pengguna Apache (XAMPP / Laragon / WampServer):**
> Arahkan *VirtualHost* atau *DocumentRoot* web server Anda langsung ke direktori `public/`. File `.htaccess` di root dan direktori `public/` sudah dikonfigurasi untuk menangani rewrite URL secara otomatis.

---

## 📁 Struktur Direktori

```
simple-php-mvc-framework/
├── application/
│   ├── controllers/         # Tempat Controller aplikasi (e.g. HomeController.php)
│   ├── functions/           # Helper fungsi global (helpers.php)
│   ├── models/              # Kelas Model PDO (Active Record pattern)
│   └── views/               # Tampilan template dan view (.view.php)
├── config/
│   └── config.php           # Inisialisasi konfigurasi, konstanta, dan loader .env
├── library/
│   ├── controller.class.php # Kelas dasar Controller
│   ├── model.class.php      # Kelas dasar Model (PDO Prepared Statements)
│   ├── security.class.php   # Utilitas Keamanan (CSRF, Hash, XSS)
│   └── view.class.php       # Engine rendering View
├── public/
│   ├── css/                 # Aset CSS
│   ├── js/                  # Aset JavaScript
│   ├── img/                 # Gambar & ikon
│   ├── .htaccess            # URL rewrite untuk Apache
│   └── index.php            # Front Controller & entry point utama
├── tmp/
│   └── log/                 # Tempat penyimpanan file error.log
├── .env                     # File konfigurasi lokal (diabaikan oleh git)
├── .env.example             # Template konfigurasi lingkungan
├── .gitignore               # Konfigurasi file yang diabaikan git
├── .htaccess                # Redirect request root ke folder public/
└── README.md                # Dokumentasi proyek
```

---

## 🛠️ Panduan Pengembangan

### 1. Cara Kerja Routing
Pola URL framework ini adalah:
```text
http://domain.com/{controller}/{action}/{param1}/{param2}
```
* URL `/` secara otomatis memanggil `HomeController` dan aksi `index()`.
* URL `/product` memanggil `ProductController` dan aksi `index()`.
* URL `/product/detail/5` memanggil `ProductController`, menjalankan metode `detail(5)` dengan parameter `5`.

---

### 2. Membuat Controller Baru

Buat berkas controller di dalam folder `application/controllers/` dengan akhiran `Controller.php`. Semua controller harus mewarisi kelas `Controller`:

```php
<?php
// application/controllers/ProductController.php

class ProductController extends Controller
{
    // Akses via: /product atau /product/index
    public function index(): void
    {
        // Memuat model Product
        $productModel = $this->model('Product');
        $products = $productModel->all('created_at', 'DESC');

        // Render view di dalam layout template
        $this->template('product/index', [
            'page_title' => 'Katalog Produk',
            'products'   => $products,
        ]);
    }

    // Akses via: /product/detail/{id}
    public function detail($id = null): void
    {
        if (empty($id)) {
            $this->redirect('product');
        }

        $product = $this->model('Product')->find($id);

        if (!$product) {
            $this->redirect('home/error/404');
        }

        $this->template('product/detail', [
            'page_title' => $product['name'],
            'product'    => $product,
        ]);
    }

    // Contoh REST API JSON: /product/api_list
    public function api_list(): void
    {
        $products = $this->model('Product')->all();
        $this->json([
            'status' => 'success',
            'data'   => $products,
        ]);
    }
}
```

---

### 3. Membuat Model Baru (Database Access)

Buat model di dalam folder `application/models/` dengan akhiran `.model.php`. Model mewarisi kelas `Model` dan secara otomatis terhubung ke database via PDO:

```php
<?php
// application/models/product.model.php

class ProductModel extends Model
{
    // Nama tabel database
    protected string $_table = 'products';

    // Primary key tabel
    protected string $_primaryKey = 'id';
}
```

#### Metode CRUD Bawaan pada Model:

```php
$model = $this->model('Product');

// 1. Mengambil semua data
$items = $model->all();
$itemsSorted = $model->all('price', 'DESC', 10); // order by price DESC limit 10

// 2. Mencari data berdasarkan Primary Key
$item = $model->find(1);

// 3. Mengambil data dengan kondisi WHERE (Prepared Statements)
$activeItems = $model->where(['status' => 'active', 'category_id' => 2]);

// 4. Mengambil 1 data pertama yang cocok
$user = $model->first(['email' => 'user@example.com']);

// 5. Menambah data baru (Return ID data baru)
$newId = $model->create([
    'name'  => 'Sepatu Lari',
    'price' => 750000,
    'stock' => 15,
]);

// 6. Memperbarui data
$affectedRows = $model->update(
    ['price' => 700000, 'stock' => 12], // Data yang diubah
    ['id' => $newId]                     // Kondisi WHERE
);

// 7. Menghapus data
$model->delete(['id' => $newId]);

// 8. Menghitung jumlah data
$total = $model->count(['status' => 'active']);

// 9. Raw Query dengan parameter binding
$stmt = $model->query("SELECT * FROM products WHERE price > :min_price", ['min_price' => 500000]);
$results = $stmt->fetchAll();
```

---

### 4. Membuat View & Bekerja dengan Template

Setiap view disimpan di dalam direktori `application/views/` dengan ekstensi `.view.php`.

#### View Konten (`application/views/product/index.view.php`):
```php
<div class="card">
    <h2><?= e($page_title) ?></h2>
    <p>Daftar produk yang tersedia di toko kami:</p>

    <ul>
        <?php foreach ($data['products'] as $item): ?>
            <li>
                <strong><?= e($item['name']) ?></strong> - Rp <?= e(number_format($item['price'])) ?>
                <a href="<?= base_url('product/detail/' . $item['id']) ?>">Lihat Detail &rarr;</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
```

---

### 5. Formulir & Proteksi CSRF

Framework menyediakan token CSRF otomatis untuk melindungi formulir Anda dari serangan *Cross-Site Request Forgery*.

#### Pada View:
```html
<form method="POST" action="<?= base_url('product/save') ?>">
    <!-- Menyisipkan input hidden CSRF token -->
    <?= csrf_field() ?>

    <label for="name">Nama Produk:</label>
    <input type="text" id="name" name="name" required>

    <button type="submit">Simpan</button>
</form>
```

#### Pada Controller:
```php
public function save(): void
{
    // 1. Verifikasi request method dan CSRF token
    if (!$this->isPost() || !csrf_verify()) {
        http_response_code(403);
        die('Akses ditolak: Token CSRF tidak valid atau sesi berakhir.');
    }

    // 2. Ambil input yang aman
    $name = $this->input('name');

    // 3. Simpan ke database
    $this->model('Product')->create([
        'name' => $name
    ]);

    // 4. Redirect kembali
    $this->redirect('product');
}
```

---

## 🧰 Referensi Helper Bawaan

| Fungsi Helper | Keterangan |
| :--- | :--- |
| `e($string)` | Melakukan sanitasi output HTML (XSS prevention) menggunakan `htmlspecialchars(..., UTF-8)`. |
| `csrf_token()` | Mengambil atau men-generate token CSRF string untuk sesi aktif. |
| `csrf_field()` | Menghasilkan tag `<input type="hidden" name="csrf_token" value="...">`. |
| `csrf_verify(?$token)` | Memverifikasi kecocokan token CSRF yang dikirimkan melalui form POST atau header. |
| `base_url($path)` | Menghasilkan URL absolut aplikasi (contoh: `http://localhost:8000/product`). |
| `asset($path)` | Menghasilkan URL file aset publik (contoh: `http://localhost:8000/css/app.css`). |
| `nav_item($route, $label)` | Membuat elemen `<li><a class="nav-link active">` dengan status active otomatis. |
| `slugify($text)` | Mengubah teks judul menjadi string permalink URL ramah SEO. |
| `current_url()` | Mengambil URL lengkap yang sedang diakses di browser. |
| `env($key, $default)` | Mengambil nilai dari file `.env` dengan fallback default. |

---

## 🔒 Praktik Keamanan

Framework ini mengedepankan keamanan bawaan (*secure-by-default*):
1. **Tidak Ada String Concatenation pada SQL**: Seluruh parameter database dipisahkan melalui prepared query PDO.
2. **Sanitasi Output**: Selalu bungkus teks dinamis dengan `e(...)` saat menampilkannya di view untuk mencegah XSS.
3. **Session Cookies**: Cookie sesi diatur dengan flag `HttpOnly`, `SameSite=Lax`, dan `Secure` (saat berjalan di protokol HTTPS).
4. **Environment Isolation**: File `.env` sudah masuk ke `.gitignore` untuk mencegah bocornya data sensitif ke repository git.

---

## 📄 Lisensi

Framework ini dirilis di bawah lisensi terbuka [MIT License](https://opensource.org/licenses/MIT). Bebas digunakan, dimodifikasi, dan didistribusikan untuk keperluan pribadi maupun komersial.
