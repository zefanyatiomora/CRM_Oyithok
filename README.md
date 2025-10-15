# Laravel CRM - Manajemen Hubungan Pelanggan

Sebuah aplikasi CRM (Customer Relationship Management) yang dibangun menggunakan framework Laravel. Aplikasi ini berfungsi untuk mengelola data produk, kategori, dan pengguna secara efisien. Proyek ini dioptimalkan dengan tabel data yang interaktif dan fitur untuk mengekspor data ke dalam format PDF.

## Fitur Utama

-   **Manajemen Pengguna:** Mengelola data pengguna beserta level aksesnya (misal: Admin, Staf).
-   **Manajemen Produk:** Fungsi CRUD (Create, Read, Update, Delete) lengkap untuk data produk.
-   **Manajemen Kategori:** Mengelompokkan produk berdasarkan kategori yang dapat dikelola.
-   **Tabel Interaktif:** Didukung oleh **Yajra Datatables** untuk pencarian, sorting, dan paginasi data yang cepat tanpa perlu me-refresh halaman.
-   **Ekspor ke PDF:** Membuat laporan atau daftar data dalam format PDF dengan mudah menggunakan **Barryvdh DomPDF**.

---

## Persiapan Awal

Sebelum melanjutkan ke proses instalasi, pastikan lingkungan pengembangan Anda telah memenuhi persyaratan berikut:

### Prasyarat

-   **Laragon** (Server Lokal)
-   **Git**
    Jika belum punya Git (untuk clone repository), unduh di:
    👉 https://git-scm.com/downloads
-   **Composer**
    Pastikan Composer sudah terinstal di sistem:

    ```bash
    composer-v
    ```

    Jika belum ada, install di:
    https://getcomposer.org/download/

---

## Panduan Instalasi Lokal

Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan proyek di mesin lokal Anda.

### 1. Clone Repositori

Buka terminal atau command prompt, lalu clone repositori proyek ke direktori lokal Anda.

```bash
git clone https://github.com/zefanyatiomora/CRM_Oyithok.git
cd CRM_Oyithok
```

### 2. Install Dependensi

Instal semua dependensi PHP yang dibutuhkan proyek melalui Composer.
Bash

```bash
composer install
```

### 3. Konfigurasi Env

Salin file konfigurasi lingkungan dari contoh yang sudah ada.
Bash

```bash
cp .env.example .env
```

Selanjutnya, buka file .env yang baru dibuat dan sesuaikan konfigurasi koneksi database Anda.

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_laravel_crm
DB_USERNAME=root
DB_PASSWORD=
```

Setelah konfigurasi database selesai, generate application key unik untuk proyek Anda.

```bash
php artisan key:generate
```

### 4. Instalasi Package Tambahan

Proyek ini memerlukan dua package eksternal yang harus diinstal melalui Composer.

-   **Instal Yajra Datatables:**
    Untuk menangani tabel data yang interaktif.

    ```bash
    composer require yajra/laravel-datatables-oracle
    ```

-   **Instal Barryvdh DomPDF:**
    Untuk fungsionalitas ekspor data ke PDF.

    ```bash
    composer require barryvdh/laravel-dompdf
    ```

### 5. Migrate & Seeding Database

Langkah ini akan membuat struktur tabel di database dan mengisinya dengan data awal.

-   Jalankan Migrasi
    Perintah ini akan membuat semua tabel yang didefinisikan dalam direktori database/migrations.

    ```bash
    php artisan migrate
    ```

-   Jalankan Seeder (Sesuai Urutan)
    Isi tabel dengan data awal. PENTING: Jalankan seeder sesuai urutan berikut untuk menjaga integritas relasional data.

    ```bash
    # 1. Mengisi tabel levels
    php artisan db:seed --class=LevelSeeder

    # 2. Mengisi tabel users (membutuhkan data dari LevelSeeder)
    php artisan db:seed --class=UserSeeder

    # 3. Mengisi tabel kategori
    php artisan db:seed --class=KategoriSeeder

    # 4. Mengisi tabel produk (membutuhkan data dari KategoriSeeder)
    php artisan db:seed --class=ProdukSeeder

    ```

### 6. Jalankan Server

Terakhir, jalankan server pengembangan lokal bawaan Laravel.

```bash
php artisan serve
```

Aplikasi Anda kini siap diakses melalui browser di alamat http://127.0.0.1:8000.
