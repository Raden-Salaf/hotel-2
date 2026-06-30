# 🏨 Paijo's Hotel — Hotel Management System

Sistem manajemen hotel berbasis web yang dibangun menggunakan **Laravel 13**. Aplikasi ini mencakup pengelolaan kamar, booking (online & walk-in), pemesanan makanan/minuman (F&B), layanan laundry, hingga integrasi pembayaran online melalui **Midtrans**.

Project ini dibuat sebagai sarana pembelajaran Laravel secara hands-on, mulai dari setup database, autentikasi & role, CRUD, hingga integrasi payment gateway dan deployment.

---

## ✨ Fitur Utama

### 👥 Multi Role
- **Super Admin** — akses penuh ke seluruh fitur sistem
- **Resepsionis** — kelola booking, check-in/check-out tamu, invoice
- **Admin F&B** — kelola menu makanan/minuman & pesanan masuk

### 🛏️ Manajemen Kamar
- CRUD kamar lengkap dengan kategori, fasilitas, dan foto
- Status kamar otomatis (tersedia, terisi, maintenance)
- Upload gambar dengan crop otomatis

### 📅 Sistem Booking
- **Booking Online** — tamu dapat memesan kamar sendiri melalui landing page publik
- **Booking Walk-in** — resepsionis input langsung untuk tamu yang datang ke lokasi
- Alur status booking: `pending → confirmed → checked_in → checked_out`
- Generate kode booking & nomor invoice otomatis

### 🍽️ Food & Beverage (F&B)
- Kategori menu (makanan, minuman, dessert, dll)
- Tamu yang sudah *confirmed*/*checked-in* dapat memesan F&B tambahan
- Admin F&B dapat membuat pesanan baru untuk tamu tertentu
- Tracking status pesanan: `pending → preparing → delivered`

### 👕 Layanan Laundry
- Manajemen item & tarif laundry (per pcs / per kg)
- Tamu aktif dapat memesan layanan laundry
- Tracking status: `pending → processing → done`
- Otomatis terintegrasi ke invoice tamu

### 💳 Integrasi Pembayaran (Midtrans)
- Pembayaran online via Midtrans Snap (Sandbox)
- Mendukung berbagai metode: GoPay, Virtual Account, Kartu Kredit, dll
- Invoice otomatis berubah status menjadi **lunas** setelah pembayaran berhasil

### 🧾 Invoice
- Invoice otomatis ter-generate untuk setiap booking
- Menampilkan rincian kamar, F&B, dan laundry dalam satu invoice
- Fitur cetak invoice dengan tampilan rapi (print-friendly)

### 🎨 UI/UX
- Desain modern dengan tema warna hijau, dibangun di atas **Tailwind CSS**
- Animasi halus menggunakan **Alpine.js**
- Notifikasi interaktif dengan **SweetAlert2**
- Responsive — dapat diakses dari desktop maupun mobile

---

## 🛠️ Tech Stack

| Kategori           | Teknologi                          |
|---------------------|-------------------------------------|
| Backend             | Laravel 13                         |
| Frontend            | Blade, Tailwind CSS, Alpine.js     |
| Database            | MySQL / MariaDB                    |
| Role & Permission   | Spatie Laravel Permission          |
| Notifikasi UI       | SweetAlert2                        |
| Icon                | Tabler Icons                       |
| Payment Gateway     | Midtrans (Sandbox)                 |
| Deployment          | Railway                            |

---

## 📁 Struktur Role & Akses

```

super_admin   → akses penuh (kamar, kategori, F\&B, laundry, user, booking)
resepsionis   → booking, check-in/out, invoice, F\&B & laundry untuk tamu
admin_fnb     → kelola menu F\&B & pesanan masuk

````

---

## 🚀 Instalasi Lokal

### Prasyarat
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL/MariaDB

### Langkah Instalasi

```bash
# Clone repository
git clone <repository-url>
cd hotel-management

# Install dependency PHP
composer install

# Install dependency JS
npm install

# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
````

Atur konfigurasi database & Midtrans di file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_management
DB_USERNAME=root
DB_PASSWORD=

MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

Lanjutkan dengan migrasi database, seeder, dan storage link:

```bash
# Migrasi & seed database
php artisan migrate --seed

# Buat symbolic link untuk storage (upload gambar)
php artisan storage:link

# Jalankan server + Vite (development)
composer run dev
```

Akses aplikasi di `http://localhost:8000`

### Akun Default (dari seeder)

| Role         | Email                  | Password    |
|--------------|-------------------------|-------------|
| Super Admin  | superadmin@hotel.com    | password123 |
| Resepsionis  | resepsionis@hotel.com   | password123 |
| Admin F&B    | fnb@hotel.com           | password123 |

---

## 📌 Catatan Pengembangan

- Pembayaran Virtual Account (VA) di mode sandbox memerlukan konfirmasi manual melalui [Midtrans Simulator](https://simulator.sandbox.midtrans.com) karena webhook tidak dapat menjangkau environment lokal/development.
- Status pembayaran untuk VA dapat dicek ulang secara manual melalui tombol **"Cek Status Pembayaran"** pada halaman konfirmasi.

---

## 🗺️ Roadmap

- [x] Setup project, autentikasi & role
- [x] CRUD kamar, kategori, F&B, laundry
- [x] Sistem booking online & walk-in
- [x] Integrasi Midtrans (Sandbox)
- [x] Invoice terintegrasi (kamar + F&B + laundry)
- [x] Manajemen user
- [x] Deploy ke Railway
- [ ] Laporan & analitik (revenue, occupancy rate)
- [ ] Notifikasi email otomatis
- [ ] Mode produksi Midtrans

---

## 📄 Lisensi

Project ini dibuat untuk tujuan pembelajaran dan portofolio pribadi.

---

<p align="center">Dibangun dengan ❤️ menggunakan Laravel — Paijo's Hotel © 2026</p>
