# Sistem Kasir (Point of Sale)

Aplikasi web **Sistem Kasir (Point of Sale)** berbasis **PHP Native** dan **PostgreSQL** dengan menerapkan pola arsitektur **MVC (Model-View-Controller)** sederhana yang bersih, modular, dan terstruktur.

---

## Fitur Utama

- **Katalog Produk Dinamis**: Menampilkan daftar produk dengan harga dan ketersediaan stok secara *real-time*.
- **Keranjang Belanja Interaktif**: Tambah item, ubah kuantitas, validasi batas stok, dan hitung subtotal secara instan di sisi klien.
- **Kalkulasi Pembayaran & Kembalian Real-time**: Perhitungan kembalian otomatis dengan indikator visual apabila uang bayar belum mencukupi.
- **Integritas Transaksi (ACID)**: Transaksi penjualan menggunakan PostgreSQL Transaction (`BEGIN`, `COMMIT`, `ROLLBACK`) untuk memastikan pengurangan stok dan pencatatan transaksi berjalan atomik dan aman.
- **Tampilan Struk Transaksi**: Struk digital rapi dengan nomor invoice / transaksi setelah checkout berhasil.
- **Desain Modern & Minimalis**: Tampilan elegan bergaya monokrom dengan tipografi *DM Sans* & *DM Mono*.

---

## Teknologi & Kebutuhan Sistem

- **Bahasa Pemrograman**: PHP >= 8.0 (dengan ekstensi `pdo_pgsql` aktif)
- **Database**: PostgreSQL >= 12
- **Frontend**: HTML5, CSS3 (Vanilla CSS), Vanilla JavaScript
- **Web Server**: Apache / Nginx / PHP Built-in Server

---

## Struktur Direktori

```text
Sistem Kasir/
├── app/
│   ├── Controllers/
│   │   └── KasirController.php  # Menangani alur logika aplikasi kasir
│   ├── Models/
│   │   ├── ProdukModel.php      # Query data & manipulasi stok produk
│   │   └── TransaksiModel.php   # Manajemen transaksi & detail transaksi
│   └── Views/
│       ├── kasir.php            # Halaman utama antarmuka kasir
│       └── struk.php            # Tampilan struk bukti transaksi
├── config/
│   └── database.php             # Konfigurasi koneksi PDO PostgreSQL
├── public/
│   ├── .htaccess                # Konfigurasi URL rewriting Apache
│   └── index.php                # Entry point & routing aplikasi
├── .env.example                 # Template variabel lingkungan
├── .gitignore                   # Daftar file yang diabaikan git
└── README.md                    # Dokumentasi proyek
```

---

## Skema Database

Jalankan perintah SQL berikut di PostgreSQL untuk membuat tabel dan data awal:

```sql
-- 1. Buat Database
CREATE DATABASE kasir_db;

-- Hubungkan ke database kasir_db sebelum menjalankan query di bawah

-- 2. Tabel Produk
CREATE TABLE produk (
    id SERIAL PRIMARY KEY,
    nama_produk VARCHAR(100) NOT NULL,
    harga NUMERIC(12, 2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabel Transaksi
CREATE TABLE transaksi (
    id SERIAL PRIMARY KEY,
    total_harga NUMERIC(12, 2) NOT NULL,
    uang_bayar NUMERIC(12, 2) NOT NULL,
    kembalian NUMERIC(12, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Tabel Detail Transaksi
CREATE TABLE detail_transaksi (
    id SERIAL PRIMARY KEY,
    transaksi_id INT NOT NULL REFERENCES transaksi(id) ON DELETE CASCADE,
    produk_id INT NOT NULL REFERENCES produk(id),
    jumlah INT NOT NULL,
    subtotal NUMERIC(12, 2) NOT NULL
);

-- 5. Data Awal (Dummy Data)
INSERT INTO produk (nama_produk, harga, stok) VALUES
('Kopi Susu Gula Aren', 18000, 25),
('Americano Ice', 15000, 30),
('Matcha Latte', 22000, 15),
('Croissant Butter', 20000, 10),
('Roti Bakar Cokelat Keju', 16000, 20),
('Air Mineral 600ml', 5000, 50);
```

---

## Panduan Instalasi & Menjalankan

### 1. Clone Repositori
```bash
git clone https://github.com/Fazani07/Sistem-Kasir.git
cd "Sistem-Kasir"
```

### 2. Konfigurasi Environment (.env)
Salin template `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan kredensial database PostgreSQL pada file `.env`:
```env
DB_HOST=localhost
DB_PORT=5432
DB_NAME=kasir_db
DB_USER=postgres
DB_PASS=password_postgres_anda
```

> **Catatan**: Pastikan ekstensi `extension=pdo_pgsql` dan `extension=pgsql` sudah diaktifkan pada file `php.ini`.

### 3. Menjalankan Server Lokal

#### Menggunakan PHP Built-in Server:
Jalankan perintah berikut pada terminal di root direktori proyek:
```bash
php -S localhost:8000 -t public
```
Akses aplikasi melalui browser di:
```text
http://localhost:8000
```

#### Menggunakan Web Server (Apache / Laragon / XAMPP):
Pastikan Document Root diarahkan ke folder `public/`, atau akses melalui path direktori:
```text
http://localhost/Sistem-Kasir/public/
```

---

## Alur Kerja Aplikasi

1. **Memilih Produk**: Klik pada kartu produk di panel kiri untuk memasukkan barang ke keranjang belanja.
2. **Mengatur Kuantitas**: Gunakan tombol `+` dan `−` pada daftar keranjang untuk menambah atau mengurangi jumlah pesanan.
3. **Input Pembayaran**: Masukkan nominal uang bayar pada input yang tersedia. Sistem akan otomatis menghitung kembalian.
4. **Proses Checkout**: Klik tombol **Proses Pembayaran**. Sistem akan:
   - Memvalidasi nominal pembayaran dan ketersediaan stok.
   - Mengurangi stok produk di database secara otomatis.
   - Menyimpan rekaman transaksi dan detail transaksi.
5. **Cetak / Lihat Struk**: Struk pembayaran ditampilkan dan kasir dapat memulai transaksi baru.

---

## Pengembang

Dibuat untuk keperluan Praktikum Pemrograman Web.
