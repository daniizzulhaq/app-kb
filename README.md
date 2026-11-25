# SI KB RS — RSUD Luwuk

Laravel 12, MySQL, Blade, Tailwind CSS 4, Chart.js 4. Ekspor `.xlsx` menggunakan PhpSpreadsheet dan PDF menggunakan Dompdf. Seluruh aset dibundel lokal, tanpa CDN.

## Menjalankan aplikasi

Kebutuhan: PHP 8.4.1+, Composer, Node.js 22+, MySQL/MariaDB, ekstensi PHP `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`, `bcmath`, `xml`.

```powershell
composer install
npm.cmd install
Copy-Item .env.example .env
php artisan key:generate
```

Buat database MySQL bernama `kb_pelayanan` dengan charset `utf8mb4`. Konfigurasi lokal `.env` memakai host `127.0.0.1`, port `3306`, username `root`, dan password kosong. Sebelum membuat akun pertama, isi `INITIAL_ADMIN_PASSWORD`, `INITIAL_PETUGAS_PASSWORD`, dan `INITIAL_PIMPINAN_PASSWORD` pada `.env` dengan kata sandi yang berbeda, masing-masing 12–128 karakter.

```powershell
php artisan migrate --seed
npm.cmd run build
php artisan serve --host=127.0.0.1 --port=8078
```

Buka http://127.0.0.1:8078. Port ini dipilih agar tidak bentrok dengan aplikasi lokal lain. Untuk pengembangan CSS/JS jalankan `npm.cmd run dev` pada terminal terpisah.

## Akun operasional

| Peran | Username | Nama akun |
| --- | --- | --- |
| Admin | admin | Administrator RSUD Luwuk |
| Petugas | petugas | Petugas Pelayanan KB |
| Pimpinan | pimpinan | Pimpinan RSUD Luwuk |

Kata sandi tidak ditulis di source code atau panduan ini. Seeder mengambil kata sandi pertama dari konfigurasi `.env` dan tidak mengganti kata sandi akun yang sudah ada. Login menggunakan username atau email resmi jika telah diisi. Email bersifat opsional; nama pengguna dan email staf yang sebenarnya dapat diatur lewat Manajemen pengguna. Akun baru disimpan dengan password hash Laravel.

Admin dapat mengubah kata sandi akun melalui Manajemen pengguna. `MasterDataSeeder` menyediakan **24 kecamatan Kabupaten Banggai** dan **7 ruangan pelayanan**: Ruang PONEK, Ruang ASOKA, Ruang Poli kandungan, Instalasi Bedah Sentral, Ruang VIP Tulip, Ruang VIP Sakura, dan Ruang VIP Bougenville. Ruangan dikelompokkan di wilayah Luwuk; ruang VIP memakai jenis Perawatan. Kode kecamatan berawalan `BANGGAI-` adalah pengenal internal aplikasi, bukan kode wilayah administratif resmi.

Untuk menambahkan master ini ke database yang sudah berjalan, jalankan `php artisan db:seed --class=MasterDataSeeder`. Seeder aman dijalankan ulang: kecamatan tidak digandakan, nama ruangan contoh lama disesuaikan dengan mempertahankan ID dan riwayat pelayanan, serta wilayah lama yang digunakan peserta tetap dipertahankan. Seeder ini tidak menambahkan akun, peserta, pelayanan, atau target.

Di luar lingkungan production, `DatabaseSeeder` juga menyediakan satu peserta sintetis, satu pelayanan, satu jadwal kontrol, satu target bulanan dan satu target tahunan. NIK contoh `9999000000000001` dan nomor HP contoh `080000000001` bersifat sintetis. Tetap tersedia satu akun untuk masing-masing dari tiga role. Akun dan target yang sudah ada tidak direset, peserta contoh tidak ditambahkan ulang jika database telah memiliki peserta.

## Persiapan hosting

Hosting harus mendukung PHP 8.4.1+ beserta ekstensi di atas dan mengarahkan document root domain ke folder `public/`. Upload source aplikasi dan hasil `public/build/`. Jangan upload `.env` lokal, `node_modules`, berkas kredensial lokal, `bootstrap/cache/*.php`, `public/hot`, atau data sesi/log lokal. Buat `.env` khusus pada server dari `.env.example`.

Pada `.env` server, isi:

```dotenv
APP_NAME="SI KB RS"
APP_INSTITUTION="RSUD Luwuk"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://DOMAIN_ANDA
SESSION_SECURE_COOKIE=true
```

Sesuaikan koneksi MySQL dengan database hosting dan isi tiga `INITIAL_*_PASSWORD` hanya untuk pembuatan akun awal. Untuk instalasi server baru tanpa data contoh:

```sh
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AccountSeeder --force
php artisan db:seed --class=MasterDataSeeder --force
php artisan optimize
```

Jika memindahkan database yang sudah berisi data terenkripsi, gunakan `APP_KEY` dari instalasi asal dan **jangan menjalankan `key:generate`**. Biarkan direktori `storage/` dan `bootstrap/cache/` dapat ditulis oleh proses PHP. Jalankan `npm ci` dan `npm run build` di komputer pengembangan jika hosting tidak menyediakan Node.js.

Pembuatan akun lewat `AccountSeeder` tidak menambahkan peserta, pelayanan, target, atau wilayah contoh. `MasterDataSeeder` hanya menambahkan master kecamatan dan ruangan. `DatabaseSeeder` menjalankan kedua seeder tersebut dan melewati data contoh ketika `APP_ENV=production`. Master tetap dapat dikelola melalui akun Admin. Pengaturan proxy lokal mendukung ngrok; jika hosting menggunakan reverse proxy lain, sesuaikan alamat proxy tepercaya dengan konfigurasi server tersebut.

## Hak akses

- Admin: seluruh modul, pengaturan target/master/pengguna, penghapusan, dan audit log.
- Petugas: cari, tambah, ubah peserta dan pelayanan; riwayat peserta; jadwal kontrol; dashboard dan laporan. Petugas pelayanan baru ditetapkan oleh server sesuai akun yang masuk. Saat mengedit riwayat, identitas petugas aslinya dipertahankan; hanya Admin dapat menggantinya. Penghapusan hanya untuk Admin.
- Pimpinan: dashboard dan laporan agregat termasuk Excel/PDF. Tidak dapat mengakses identitas peserta atau endpoint pencatatan.
- Akun nonaktif kehilangan akses, termasuk sesi yang sedang berjalan.

## Aturan perhitungan

- Satu identitas mempunyai satu NIK. NIK terenkripsi; HMAC unik digunakan untuk validasi duplikasi dan pencarian NIK lengkap.
- Jumlah pelayanan adalah jumlah baris tindakan. Peserta unik adalah `COUNT(DISTINCT participant_id)` pada periode dan filter terpilih.
- Baru/lama mengikuti pelayanan pertama sepanjang riwayat peserta, diurutkan tanggal pelayanan lalu ID. Menambah riwayat lampau, mengubah tanggal, atau menghapus pelayanan menghitung ulang klasifikasi secara transaksional.
- Peserta baru dalam laporan hanya dihitung apabila pelayanan pertamanya sesuai periode dan filter.
- Realisasi target adalah peserta unik terlayani. Target tahunan diatur tersendiri; peserta unik tahunan tidak dijumlahkan dari realisasi bulanan.
- Diagram metode dan kolom metode laporan menghitung pelayanan. Diagram wilayah mengikuti wilayah identitas saat ini; peserta lintas ruangan dapat terhitung di masing-masing ruangan.
- Peserta aktif mengikuti tindakan kontrasepsi terakhir: Pencabutan menonaktifkan peserta; tindakan lainnya mengaktifkan peserta. Konseling tidak mengubah status sebelumnya dan konseling saja tidak mengaktifkan peserta. Status tidak menyatakan efektivitas klinis atau masa berlaku kontrasepsi. Total terdaftar dan aktif di dashboard menunjukkan kondisi terkini seluruh database; peserta baru/pelayanan mengikuti filter periode.
- Jadwal berasal dari tanggal kontrol pada pelayanan. Penyelesaian jadwal tidak otomatis membuat pelayanan baru; petugas harus mencatat tindakan yang benar-benar dilakukan. Jadwal lama tetap terjadwal sampai petugas menyelesaikan/membatalkannya.
- Laporan dapat difilter bulan, tahun, wilayah peserta, ruangan pelayanan, dan metode. Jika dimensi wilayah/ruangan/metode difilter, kartu target global tidak ditampilkan agar perbandingan tetap bermakna.

## PWA dan pemasangan di HP

SI KB RS mendukung pemasangan ke layar utama dan membuka aplikasi dalam mode `standalone`. Gunakan domain hosting dengan HTTPS; ngrok HTTPS dan localhost dapat dipakai untuk pemeriksaan lokal. Pada browser Android yang mendukung, tombol **Pasang** muncul ketika browser mengizinkan pemasangan; pengguna juga dapat memakai menu browser **Instal aplikasi/Tambahkan ke layar utama**. Pada iPhone/iPad, buka di Safari lalu pilih **Bagikan → Tambahkan ke Layar Utama**. Ikon aplikasi menggunakan lambang Kabupaten Banggai.

Upload juga `public/manifest.webmanifest`, `public/sw.js`, `public/offline.html`, `public/icons/`, serta hasil build terbaru. Service worker hanya menyimpan aset publik seperti ikon, logo, CSS/JS, dan halaman koneksi terputus. Halaman peserta, pencarian, dashboard, laporan, sesi login, dan data formulir tidak dimasukkan ke cache PWA. Aplikasi tetap memerlukan internet untuk pencatatan dan tidak menyediakan antrean pengiriman offline. Setelah koneksi pulih, tombol **Coba lagi** memuat halaman dari server.

Jika mengganti aset offline atau ikon di kemudian hari, naikkan versi `CACHE_NAME` pada `public/sw.js` agar cache publik lama dibersihkan. Akun, hak akses, dan autentikasi tetap ditangani Laravel saat aplikasi dibuka dari ikon HP.

## Perlindungan data

Autentikasi session, regenerasi sesi, CSRF, throttling login, middleware role pada server, validasi, penguncian peserta saat perubahan riwayat, foreign key, dan audit log. NIK, nama pasangan, alamat, nomor HP, catatan pelayanan dan tindak lanjut terenkripsi pada database. NIK disamarkan dalam daftar/pencarian. Log berisi tindakan dan ID objek, bukan salinan data pribadi. Identitas lengkap hanya tersedia pada halaman detail/edit yang aksesnya dicatat. Nama, tanggal lahir dan wilayah tetap digunakan untuk operasional; batasi akses database dan cadangan.

Pertahankan `APP_KEY`: menggantinya membuat data terenkripsi tidak dapat dibaca dan mengubah HMAC NIK. Simpan cadangan kunci bersama kebijakan cadangan database. Di server operasional gunakan HTTPS, `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, dan akun MySQL khusus. Document root server harus ke direktori `public/`.

Tidak disertakan file testing atau environment testing, sesuai permintaan.
