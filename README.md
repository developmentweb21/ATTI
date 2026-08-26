# PDE Service Desk

Aplikasi ticketing Teknisi IT berbasis CodeIgniter 3 dan MySQL. Alurnya: **Baru → Diterima → Proses → Selesai Teknisi → Disetujui / Reject**.

## Menjalankan aplikasi

1. Buat database dengan mengimpor [database.sql](database.sql) di phpMyAdmin atau MySQL.
2. Atur koneksi MySQL pada [application/config/database.php](application/config/database.php): isi `username`, `password`, dan `database` menjadi `atti_ticketing`.
3. Atur `base_url` pada [application/config/config.php](application/config/config.php), misalnya `http://localhost/ATTI/`.
4. Buka `http://localhost/ATTI/`.

Akun seed memakai kata sandi `password`:

| Peran | Email |
|---|---|
| Pelapor | andi@pde.local |
| Teknisi | budi@pde.local |
| Atasan IT | citra@pde.local |
| Admin | deni@pde.local |

## Yang tersedia

- Login berbasis peran dan dashboard status berwarna.
- Pembuatan tiket oleh pelapor atau teknisi, dengan nomor format `PDE/YYYYMMDD/XXXX`.
- Teknisi menerima, memulai, serta menyelesaikan pekerjaan; kode penerimaan/selesai otomatis.
- Approval atau pengembalian tiket oleh Atasan IT.
- Audit log setiap perubahan status maupun pembaruan pekerjaan.
- Skema database mencakup master data, sparepart, tanda tangan, approval, notifikasi, dan audit trail sebagai fondasi pengembangan berikutnya.

Integrasi signature canvas, QR/barcode gambar, e-mail/WA, PDF, ekspor, dan CRUD master data telah disiapkan di level skema tetapi membutuhkan kredensial layanan dan library yang belum ada di proyek awal.

## Email Gmail

Edit [application/config/email.php](application/config/email.php), lalu isi `smtp_user` dengan alamat Gmail pengirim dan `smtp_pass` dengan Google App Password 16 karakter. Jangan gunakan password Gmail biasa dan jangan commit file itu setelah berisi kredensial.

Notifikasi email dikirim ke semua teknisi saat tiket dibuat dan ke pelapor ketika status tiket berubah. Riwayat pengiriman tersimpan pada tabel `notification_logs`.
