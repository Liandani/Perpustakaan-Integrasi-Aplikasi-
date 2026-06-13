# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan di dalam file ini.

## [0.2] - 2026-06-14

### Added
- Menambahkan skrip pengujian E2E otomatis (`test-e2e.ps1`) menggunakan PowerShell untuk validasi REST API dan RabbitMQ.
- Mengimplementasikan variabel lingkungan dinamis (`env()`) pada kontroler untuk URL layanan dan RabbitMQ Host untuk menggantikan konfigurasi statis (*hardcode*).
- Menambahkan perintah *generation* `APP_KEY` secara langsung ke dalam `Dockerfile` untuk memastikan tersedianya kunci enkripsi pada saat *build*.

### Fixed
- Memperbaiki kegagalan proses *build* Docker akibat `composer install` (Exit Code 4) dengan memperbarui *build context* menjadi *root directory* dan menyesuaikan instruksi `COPY`.
- Memperbaiki kegagalan resolusi *local symlink* di dalam kontainer Docker dengan mengubah instruksi `composer install` menjadi `composer update`.
- Memperbaiki *Bug 500 Internal Server Error* (`MissingAppKeyException`) akibat pengecualian `.dockerignore` dengan menyalin paksa `.env.example` menjadi `.env` pada langkah *build* Docker.
- Memperbaiki *Bug 500 Internal Server Error* (`SQLiteDatabaseDoesNotExistException`) di Laravel 13 akibat ketiadaan file database untuk *session driver* bawaan dengan mengeksekusi inisialisasi file SQLite dan migrasi.

---

## [0.1] - 2026-06-13

### Changed (Diubah)
- **Struktur Repositori ke Monorepo Bersih:** Menghapus seluruh kerangka bawaan Laravel (seperti folder `app/`, `routes/`, `config/`, `database/`, dan `tests/`) dari direktori utama (root).
  - *Alasan:* Direktori utama sebelumnya adalah aplikasi Monolith utuh, padahal di dalamnya terdapat folder-folder microservices. Hal ini menyebabkan kerancuan di mana logika sebenarnya dijalankan. Dengan menghapusnya, root kini murni menjadi *wadah* pengelola (Monorepo) untuk layanan-layanan di dalamnya.
- **Pembaruan Konfigurasi Docker:** Memodifikasi `docker-compose.yml` untuk menyesuaikan dengan jalur (*path*) *build* dari struktur folder `sync/` dan `async/` yang baru.

### Added (Ditambahkan)
- **Pemisahan Jalur Sync & Async:** Membuat folder `sync/` untuk layanan HTTP (REST API) dan `async/` untuk layanan pemrosesan *background* (Queue Workers/Cron). Layanan lama dipindahkan menjadi `sync/book-api`, `sync/user-api`, dll.
  - *Detail:* Memindahkan *Controllers* bisnis yang dulunya berada di direktori utama (`app/Http/Controllers/BookController.php`, dsb.) langsung ke dalam masing-masing API service di folder `sync/`. Menggandakan kerangka aplikasi untuk `async/worker`.
  - *Alasan:* Pemisahan ini mengikuti pola arsitektur *event-driven* tingkat lanjut. Layanan `sync` hanya bertugas melayani HTTP Request dari *user* secepat mungkin, sedangkan proses berat (pengiriman email, perhitungan denda harian) dikerjakan oleh layanan `async`. Ini membuat skalabilitas lebih presisi (Anda bisa menambah server untuk API tanpa membuang sumber daya untuk *worker*, dan sebaliknya).
- **Paket Core Bersama (`packages/shared-core`):** Membuat *local composer package* baru yang berisi struktur Model database (seperti `User.php`, `Book.php`, `Loan.php`, `Fine.php`).
  - *Alasan:* Karena sekarang aplikasi API (`sync`) dan aplikasi *worker* (`async`) berdiri sendiri-sendiri secara fisik namun menggunakan basis data yang sama, mereka membutuhkan Model yang sama. Memusatkannya ke dalam satu paket mencegah terjadinya duplikasi kode (DRY - *Don't Repeat Yourself*).
- **Pembaruan Dependensi Antar Layanan:** Menambahkan skrip referensi Repositori Lokal (`type: path`) ke dalam `composer.json` pada masing-masing layanan agar bisa mengenali dan memuat `enterprise/shared-core`.
- **API Gateway Placeholder:** Menyiapkan folder `api-gateway` baru di struktur root.
  - *Alasan:* Disiapkan sebagai gerbang tunggal (*Single Point of Entry*) bagi klien *frontend* atau pihak ketiga untuk mengakses berbagai layanan mikro di belakang layar tanpa perlu mengetahui letak *port* masing-masing layanan.
- **Inisialisasi & Pengujian Awal (`sync/book-api`):** Menjalankan `composer update`, membuat file `.env`, mengatur *Application Key*, dan menjalankan pengujian (`php artisan test`) khusus di layanan `sync/book-api`.
  - *Alasan:* Untuk membuktikan dan memverifikasi bahwa struktur monorepo yang baru, termasuk pemanggilan paket lokal `shared-core`, dapat berjalan tanpa *error* autoloading.
