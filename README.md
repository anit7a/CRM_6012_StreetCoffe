# CRM 6012 Street Coffee

Aplikasi CRM berbasis PHP Native + MySQL untuk berjalan di XAMPP. Frontend menggunakan HTML, CSS, Bootstrap 5, dan JavaScript.

## Instalasi XAMPP
1. Jalankan Apache dan MySQL dari XAMPP Control Panel.
2. Salin folder `crm_6012_street_coffee` ke `C:\xampp\htdocs\`.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih menu **Import**, unggah `database.sql`, lalu klik **Import** / **Go**.
5. Buka `http://localhost/crm_6012_street_coffee/login.php`.
6. Login awal: **username `admin`**, **password `admin123`**. Akun admin dibuat otomatis saat aplikasi pertama kali terhubung jika tabel `users` masih kosong.
7. Sesuaikan konfigurasi host, database, username, dan password MySQL di `config/database.php` jika konfigurasi XAMPP berbeda.

## Fitur
- Login admin dengan `password_hash()` dan `password_verify()`.
- Dashboard ringkasan.
- CRUD dan pencarian pelanggan.
- Transaksi terhubung ke pelanggan; 1 poin setiap Rp10.000.
- Keluhan/saran dan status Baru, Diproses, Selesai.
- Segmentasi otomatis: Loyal (≥10), Aktif (4–9), Sesekali (1–3), Belum bertransaksi (0).
- Laporan sederhana dan daftar pelanggan pasif (default 30 hari).

## Catatan
- 20 pelanggan pada `database.sql` adalah **data simulasi/dummy**, bukan pelanggan asli.
- Data menu disimpan sebagai nama menu dan jumlah pembelian pada transaksi. Total pembayaran dimasukkan admin.
- Promo pelanggan pasif adalah daftar identifikasi untuk tindak lanjut admin; pengiriman promo tidak dilakukan otomatis.
- Penghapusan pelanggan tidak menghapus histori transaksi/keluhan; relasinya menjadi NULL agar catatan historis tetap ada.
- Untuk produksi, gunakan HTTPS, ubah password admin, batasi akses server, dan siapkan backup database.
