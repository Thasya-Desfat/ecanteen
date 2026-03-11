# Sistem E-Canteen Sekolah

Sistem Kantin Digital dengan fitur Multi-Tenant (Per Toko) dan Cashless (Saldo Virtual) menggunakan Laravel 11.

## 🌟 Fitur Utama

### 1. Sistem Autentikasi & Role

- **Auto Role Registration**: Pengguna otomatis mendapat role `siswa` saat registrasi
- **Multiple Roles**:
    - `siswa`: Dapat belanja dan top-up saldo
    - `admin_toko`Mengelola toko, menu, validasi top-up
    - `super_admin`: Akses penuh sistem

### 2. Multi-Tenant System

- Setiap admin toko memiliki toko sendiri
- Siswa dapat memesan dari berbagai toko sekaligus
- Dashboard antrean per toko

### 3. Cashless System

- Saldo virtual untuk setiap siswa
- Top-up menggunakan kode virtual unik
- Riwayat mutasi saldo lengkap

### 4. Pre-Order System

- Pilih waktu pengambilan (Istirahat 1/2)
- Validasi saldo otomatis
- Status pesanan real-time

## 📋 Requirements

- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM (optional, untuk build assets)

## 🚀 Instalasi

### 1. Clone Repository

```bash
cd c:\laragon\www\sistem-kantin-sekolah
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecanteen_db
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi & Seeder

```bash
php artisan migrate:fresh --seed
```

### 6. Setup Storage

```bash
php artisan storage:link
```

### 7. Jalankan Server

```bash
php artisan serve
```

Akses: `http://localhost:8000`

## 👤 Akun Testing

Setelah seeding, gunakan akun berikut untuk testing:

| Role         | Email              | Password |
| ------------ | ------------------ | -------- |
| Super Admin  | admin@ecanteen.com | password |
| Admin Toko 1 | toko1@ecanteen.com | password |
| Admin Toko 2 | toko2@ecanteen.com | password |
| Siswa        | siswa@ecanteen.com | password |

## 📁 Struktur Database

### Users Table

- `id`, `name`, `email`, `password`
- `role`: enum(siswa, admin_toko, super_admin)
- `saldo`: integer (default: 0)

### Tokos Table

- `id`, `nama_toko`, `user_id` (admin toko pemilik)

### Menus Table

- `id`, `toko_id`, `nama_menu`, `harga`, `foto`
- `status`: enum(tersedia, habis)

### Orders Table

- `id`, `user_id`, `total_harga`
- `waktu_pengambilan`: enum(Istirahat 1, Istirahat 2)
- `status`: enum(pending, diproses, siap, selesai)

### Order_Details Table

- `id`, `order_id`, `menu_id`, `quantity`, `subtotal`

### Top_Ups Table

- `id`, `user_id`, `nominal`, `kode_virtual`
- `status`: enum(pending, success)
- `expired_at`: timestamp (24 jam)

### Saldo_Histories Table

- `id`, `user_id`, `jenis` (masuk/keluar)
- `nominal`, `keterangan`, `saldo_akhir`

## 🎯 Alur Penggunaan

### Untuk Siswa:

1. **Registrasi/Login** → Otomatis mendapat role siswa
2. **Top-Up Saldo**:
    - Generate kode virtual (format: CN-XXXXXX)
    - Tunjukkan kode ke admin toko
    - Saldo bertambah setelah validasi
3. **Belanja**:
    - Pilih menu dari berbagai toko
    - Pilih waktu pengambilan
    - Checkout (saldo otomatis terpotong)
4. **Lihat Pesanan**: Cek status pesanan real-time

### Untuk Admin Toko:

1. **Login** dengan akun admin_toko
2. **Buat Toko** (jika belum ada)
3. **Kelola Menu**:
    - Tambah menu (nama, harga, foto, status)
    - Edit/hapus menu
4. **Dashboard Antrean**:
    - Lihat pesanan yang berisi menu toko Anda
    - Update status pesanan (pending → diproses → siap → selesai)
5. **Validasi Top-Up**:
    - Input kode virtual dari siswa
    - Sistem otomatis tambah saldo jika valid

## 🔒 Keamanan

### Middleware

- `CheckRole`: Membatasi akses berdasarkan role
- Routes protected dengan `auth` dan `role` middleware

### Transaction Safety

- Semua transaksi saldo menggunakan `DB::transaction()`
- Rollback otomatis jika terjadi error
- Mencegah race condition

### Validasi

- Input validation di semua form
- Check ketersediaan menu saat checkout
- Check expired kode virtual
- Validasi saldo cukup sebelum checkout

## 📝 Controllers

### AuthController

- Registrasi dengan auto role siswa
- Login/Logout

### OrderController

- `checkout()`: Validasi saldo, potong saldo, buat order, catat mutasi
- Menggunakan DB::transaction untuk safety

### TopUpController

- `generateCode()`: Generate kode virtual unik
- `validateCode()`: Validasi kode, tambah saldo, catat mutasi (DB::transaction)

### AdminTokoController

- Dashboard antrean (filter pesanan per toko)
- CRUD Menu
- Update status pesanan

### MenuController & TokoController

- Browse menu dan toko untuk siswa

## 🎨 Frontend

- **TailwindCSS**: Styling dengan CDN
- **Responsive**: Mobile-friendly
- **Interactive**: JavaScript shopping cart
- **Flash Messages**: Success/error notifications

## 📄 API Routes

### Public

- `GET /`: Welcome page
- `GET /register`, `POST /register`: Registrasi
- `GET /login`, `POST /login`: Login

### Siswa (Protected)

- `GET /menus`: Lihat semua menu
- `GET /orders`: Riwayat pesanan
- `POST /orders/checkout`: Proses checkout
- `GET /topup`: Halaman top-up
- `POST /topup/generate`: Generate kode virtual
- `GET /topup/history`: Riwayat saldo

### Admin Toko (Protected)

- `GET /admin-toko/dashboard`: Dashboard antrean
- `GET /admin-toko/menus`: Daftar menu
- `POST /admin-toko/menus`: Tambah menu
- `PUT /admin-toko/menus/{id}`: Update menu
- `DELETE /admin-toko/menus/{id}`: Hapus menu
- `POST /admin-toko/orders/{id}/update-status`: Update status order
- `GET /admin-toko/validate-topup`: Form validasi
- `POST /admin-toko/validate-topup`: Proses validasi

## 🤝 Contributing

Sistem ini dibuat untuk keperluan pembelajaran. Feel free to modify sesuai kebutuhan!

## 📧 Support

Jika ada pertanyaan atau issue, silakan buat issue di repository.

## 📜 License

Open source. Silakan digunakan untuk keperluan edukasi.

---

**Built with ❤️ using Laravel 11**
