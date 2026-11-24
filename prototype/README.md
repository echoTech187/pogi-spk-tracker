# README.md — Sistem Monitoring & Manajemen SKP Nasional POGI
**Dikembangkan & Dikelola oleh PT Energi Cipta untuk Perkumpulan Obstetri dan Ginekologi Indonesia (POGI)**

---

## 1. Tentang Sistem

**Sistem Monitoring & Manajemen SKP Nasional POGI (MedCred Enterprise)** adalah platform operasional B2B terpusat yang dirancang untuk memantau, memvalidasi, mengaudit, dan mengelola pemenuhan Satuan Kredit Profesi (SKP) seluruh Dokter Spesialis Obstetri dan Ginekologi (Sp.OG) di 38 provinsi Indonesia.

Sistem ini menjembatani pengumpulan berkas sertifikat dan logbook klinis, penyimpanan arsip digital (*Document Vault*), pengingat otomatis berkala melalui WhatsApp dengan perlindungan *anti-ban jitter*, pencatatan audit sinkronisasi ganda (*dual-timestamp*) terhadap portal SKP Kemenkes (*Plataran Sehat*), hingga pembuatan laporan resmi otomatis setiap tanggal 1 (*Monthly Cutoff Snapshot*) sebagai lampiran sah penagihan (*invoice*).

---

## 2. Fitur Utama Platform

* **Dashboard Eksekutif & Pemetaan 38 Provinsi**:
  * KPI ringkasan nasional: total dokter terpantau, rasio kepatuhan (*Complete* vs *Incomplete*), rata-rata SKP berjalan, dan persentase kepatuhan nasional.
  * **Peta Interaktif Kepatuhan 38 Provinsi**: Visualisasi status wilayah berbasis kode warna baku:
    - **Kuning (`#EAB308`) — Terpenuhi / Aman**: Capaian wilayah $\ge 85\%$.
    - **Hijau (`#16A34A`) — Hampir Memenuhi / Waspada**: Capaian wilayah $50\% \le \text{Capaian} < 85\%$.
    - **Merah (`#DC2626`) — Sangat Kurang / Kritis**: Capaian wilayah $< 50\%$.
* **Monitoring Standar SKP Kemenkes (Target 160 SKP / 5 Tahun)**:
  * Penilaian otomatis proporsi wajib sesuai **KMK No. HK.01.07/MENKES/1561/2024 & KMK No. HK.01.07/MENKES/1111/2026**:
    - **Ranah Pembelajaran**: Minimal $45\%$ ($72\text{ SKP}$).
    - **Ranah Pelayanan**: Minimal $35\%$ ($56\text{ SKP}$).
    - **Ranah Pengabdian Masyarakat**: Minimal $5\%$ ($8\text{ SKP}$).
    - **Ranah Fleksibilitas**: Maksimal $15\%$ ($24\text{ SKP}$).
* **Repositori Berkas Anggota (*Document Vault*) & Kebijakan Anti-Storage Bloat**:
  * Pengelolaan arsip berkas sertifikat dan logbook klinis berbasis **URL / Tautan Google Drive Mandatori** guna mencegah server membengkak dan cepat kehabisan kapasitas *storage*, dilengkapi fitur opsional unggah biner fisik (maksimal 10 MB per dokumen, format PDF/JPG/PNG) yang dilindungi *Presigned URL* (berlaku 15 menit) dan verifikasi checksum SHA-256.
* **Dual-Timestamp Audit Sinkronisasi**:
  * Menampilkan secara transparan waktu pembaruan internal (`Last Update Dashboard`) dan waktu sinkronisasi Kemenkes (`Last Update Kemenkes`).
* **Otomasi WhatsApp Blast dengan Anti-Ban Jitter**:
  * Pengiriman pengingat otomatis menjelang cutoff bulanan (**H-7** dan **H-2**) menggunakan sistem antrean (*queue*) dengan jeda acak 1.500 ms – 3.000 ms per pesan (maksimal 15 pesan/menit).
* **Laporan Bulanan Otomatis & Arsip PDF Permanen**:
  * Eksekusi otomatis setiap **tanggal 1 pukul 00:00 WIB** yang mengunci data (*snapshot locking*) dan merender dokumen `Monthly_Report_POGI_YYYY_MM.pdf` yang dapat diakses via *Month-Year Picker*.

---

## 3. Struktur 5 Peran Resmi (*Role-Based Access Control / RBAC*)

| No | Peran (*System Role*) | Deskripsi & Batas Wewenang |
|:---:|:---|:---|
| **1** | **Super Admin** | Akses penuh (*Full-access*), konfigurasi master data (faskes, provinsi, cabang, kategori, event, pengguna), manajemen hak akses, dan audit trail log. |
| **2** | **Admin Pusat** | Memantau aktivitas operasional seluruh cabang, memonitor SLA dan performance kerja dari tim Admin POGI, serta export laporan nasional (audit/read mode pada data dokter). |
| **3** | **Admin POGI** *(Verifikator)* | Melakukan verifikasi kesesuaian dokumen bukti fisik, validasi kredit SKP ranah, dan memantau dokter binaan di bawah penugasannya secara nasional. |
| **4** | **Admin Cabang** | Melakukan verifikasi dan monitoring berkas SKP, namun **terisolasi secara ketat khusus untuk dokter di wilayah cabangnya sendiri** (akses ke cabang lain diblokir otomatis via *Global Scopes*). |
| **5** | **User / Dokter** | Akses portal mandiri khusus untuk melihat portofolio SKP 5 tahun dan status resertifikasi dirinya sendiri. |

---

## 4. Struktur Dokumen Acuan Proyek

| Dokumen | Deskripsi & Cakupan |
|:---|:---|
| **[`README.md`](file:///c:/Users/Pongo/Downloads/pogi-spk-tracker/README.md)** | Ikhtisar produk, fitur utama, standar RBAC 5 role, dan **panduan step-by-step implementasi produksi**. |
| **[`RULES.md`](file:///c:/Users/Pongo/Downloads/pogi-spk-tracker/RULES.md)** | Seluruh aturan bisnis operasional B2B, formula perhitungan SKP 160, siklus cutoff tanggal 1, jadwal WA Blast, kode warna peta, matriks RBAC, dan kepatuhan UU PDP No. 27/2022. |
| **[`ARCHITECTURE.md`](file:///c:/Users/Pongo/Downloads/pogi-spk-tracker/ARCHITECTURE.md)** | Cetak biru Clean Architecture, diagram **ERD lengkap 18 tabel**, **DFD Level 0 & Level 1**, 4 Diagram Alur Kerja (*Workflows*), Batasan Sistem, dan Strategi Indeks *Zero Full-Scan*. |

---

## 5. Panduan Implementasi Step-by-Step (Persiapan s.d. Release Production)

Berikut adalah panduan operasional teknis tahap demi tahap untuk membangun, mengonfigurasi, dan merilis sistem ini ke lingkungan produksi menggunakan stack **Laravel 11 & PostgreSQL 14+**:

### Fase 1: Prasyarat Sistem & Kesiapan Environment

Pastikan server atau mesin pengembangan Anda telah memenuhi spesifikasi minimum berikut:
* **PHP**: Versi `8.2.x` atau `8.3.x` (Ekstensi wajib: `pdo_pgsql`, `openssl`, `mbstring`, `curl`, `bcmath`, `fileinfo`, `gd`, `intl`).
* **Composer**: Versi `2.7.x` atau lebih baru.
* **PostgreSQL**: Versi `14.x` atau `16.x` (dengan ekstensi `pg_trgm` dan `uuid-ossp` aktif).
* **Redis**: Versi `7.x` (untuk antrean pesan background *Queue Worker* dan cache).
* **Node.js & NPM**: Node `v20.x` LTS untuk kompilasi aset frontend.

---

### Fase 2: Inisialisasi Proyek Laravel 11 & Konfigurasi Lingkungan

1. Buat proyek Laravel baru:
   ```bash
   composer create-project laravel/laravel pogi-backend
   cd pogi-backend
   ```
2. Buat kunci enkripsi aplikasi:
   ```bash
   php artisan key:generate
   ```
3. Konfigurasi file `.env`:
   ```env
   APP_NAME="POGI SKP Tracker"
   APP_ENV=production
   APP_DEBUG=false
   APP_TIMEZONE="Asia/Jakarta"
   APP_URL=https://skp.pogi.or.id

   # Koneksi Database PostgreSQL
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=pogi_skp_tracker
   DB_USERNAME=postgres
   DB_PASSWORD=SecureProductionPassword2026!

   # Queue & Cache
   QUEUE_CONNECTION=redis
   CACHE_STORE=redis
   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379

   # Storage (Opsional jika ada upload biner; Bukti fisik utama mandatori berbasis Google Drive URL)
   FILESYSTEM_DISK=s3
   AWS_ACCESS_KEY_ID=your_minio_or_s3_key
   AWS_SECRET_ACCESS_KEY=your_minio_or_s3_secret
   AWS_DEFAULT_REGION=ap-southeast-1
   AWS_BUCKET=pogi-medcred-vault
   AWS_ENDPOINT=https://vault.energicipta.id

   # WhatsApp Gateway Provider
   WA_GATEWAY_URL="https://api.wagateway.energicipta.id/v1"
   WA_API_TOKEN="pogi_secure_token_wa_blast"
   ```

---

### Fase 3: Provisioning Database PostgreSQL & Eksekusi Seeder Bebas Error

1. Buat database di PostgreSQL:
   ```bash
   psql -U postgres -c "CREATE DATABASE pogi_skp_tracker WITH OWNER postgres ENCODING 'UTF8';"
   ```
2. Muat skema database produksi terindeks penuh ([`schema.sql`](file:///c:/Users/Pongo/Downloads/pogi-spk-tracker/schema.sql)):
   ```bash
   psql -U postgres -d pogi_skp_tracker -f schema.sql
   ```
3. Muat data awal komprehensif ([`seeds.sql`](file:///c:/Users/Pongo/Downloads/pogi-spk-tracker/seeds.sql)):
   ```bash
   psql -U postgres -d pogi_skp_tracker -f seeds.sql
   ```
   > [!NOTE]
   > File `seeds.sql` telah diaudit 100% mematuhi format **RFC 9562 UUIDv7** (`01935d...`), bebas dari pelanggaran integritas referensial, dan menggunakan klausul `ON CONFLICT` sehingga aman dieksekusi berulang kali tanpa error.

---

### Fase 4: Scaffolding Clean Architecture di Laravel

Buat struktur folder arsitektur bersih di dalam folder `app/`:

```bash
mkdir -p app/Domain/Entities app/Domain/ValueObjects app/Domain/Enums app/Domain/Repositories app/Domain/Events
mkdir -p app/Application/UseCases/Doctor app/Application/UseCases/Skp app/Application/UseCases/Cutoff app/Application/DTOs
mkdir -p app/Infrastructure/Persistence/Repositories app/Infrastructure/External/WhatsApp app/Infrastructure/External/Storage
mkdir -p app/Presentation/Http/Controllers app/Presentation/Http/Requests app/Presentation/ViewModels
```

1. **Registrasi Service Provider Binding**:
   Di `app/Providers/AppServiceProvider.php`, hubungkan antarmuka domain dengan implementasi infrastruktur:
   ```php
   use App\Domain\Repositories\DoctorRepositoryInterface;
   use App\Infrastructure\Persistence\Repositories\EloquentDoctorRepository;
   use App\Domain\Repositories\SkpEntryRepositoryInterface;
   use App\Infrastructure\Persistence\Repositories\EloquentSkpEntryRepository;

   public function register(): void
   {
       $this->app->bind(DoctorRepositoryInterface::class, EloquentDoctorRepository::class);
       $this->app->bind(SkpEntryRepositoryInterface::class, EloquentSkpEntryRepository::class);
   }
   ```

2. **Pasang Global Scope Isolasi Cabang**:
   Buat `app/Models/Scopes/BranchScope.php` untuk membatasi query Admin Cabang secara otomatis:
   ```php
   public function apply(Builder $builder, Model $model): void
   {
       $user = Auth::user();
       if ($user && $user->role === 'ADMIN_CABANG' && $user->branch_id) {
           $builder->where($model->getTable() . '.branch_id', $user->branch_id);
       }
   }
   ```

---

### Fase 5: Migrasi Antarmuka Tampilan (Blade Views)

Pindahkan seluruh markup prototype HTML ke folder `resources/views/`:
1. Buat layout utama: `resources/views/layouts/app.blade.php` (sidebar navigasi, role switcher, dan notifikasi toast).
2. Pindahkan tampilan modul:
   - Dashboard: `resources/views/dashboard.blade.php` (dari `index.html`)
   - Dokter & Portofolio: `resources/views/doctors/index.blade.php` dan `resources/views/doctors/show.blade.php` (dari `doctors.html` dan `doctor-detail.html`)
   - Pengaturan Master: `resources/views/manage/index.blade.php` (dari `manage.html`)
   - Admin Performance: `resources/views/admin-performance.blade.php` (dari `admin-performance.html`)
   - Laporan Bulanan: `resources/views/reports.blade.php` (dari `reports.html`)
3. Kompilasi aset frontend:
   ```bash
   npm install && npm run build
   ```

---

### Fase 6: Konfigurasi Background Queue Worker & Task Scheduler

1. **Konfigurasi Task Scheduler di Crontab Server**:
   Buka crontab server produksi:
   ```bash
   crontab -e
   ```
   Tambahkan satu baris berikut untuk menjalankan scheduler Laravel setiap menit:
   ```text
   * * * * * cd /var/www/pogi-backend && php artisan schedule:run >> /dev/null 2>&1
   ```

2. **Konfigurasi Supervisor Daemon untuk Queue Worker (WA Blast & Cutoff)**:
   Buat file `/etc/supervisor/conf.d/pogi-worker.conf`:
   ```ini
   [program:pogi-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /var/www/pogi-backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
   autostart=true
   autorestart=true
   stopasgroup=true
   killasgroup=true
   user=www-data
   numprocs=2
   redirect_stderr=true
   stdout_logfile=/var/www/pogi-backend/storage/logs/worker.log
   stopwaitsecs=3600
   ```
   Aktifkan supervisor:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start pogi-worker:*
   ```

---

### Fase 7: Pengujian & Validasi Kualitas (*Testing & QA*)

1. **Unit Testing Use Cases**:
   ```bash
   php artisan test --testsuite=Unit
   ```
2. **Validasi Kinerja Database (Zero Full-Scan Audit)**:
   Uji query utama menggunakan `EXPLAIN ANALYZE` di terminal `psql` untuk memastikan seluruh query menggunakan indeks:
   ```sql
   EXPLAIN ANALYZE SELECT * FROM doctors WHERE full_name ILIKE '%Simatupang%';
   -- Pastikan output menunjukkan 'Bitmap Index Scan on idx_doctors_trgm_search', BUKAN 'Seq Scan'
   ```

---

### Fase 8: Pengerasan Keamanan & Panduan Rilis Produksi (*Production Release Runbook*)

1. **Optimasi Cache Laravel Produksi**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   ```
2. **Konfigurasi Nginx Web Server**:
   Buat file `/etc/nginx/sites-available/pogi-skp.conf`:
   ```nginx
   server {
       listen 80;
       server_name skp.pogi.or.id;
       return 301 https://$host$request_uri;
   }

   server {
       listen 443 ssl http2;
       server_name skp.pogi.or.id;

       ssl_certificate /etc/letsencrypt/live/skp.pogi.or.id/fullchain.pem;
       ssl_certificate_key /etc/letsencrypt/live/skp.pogi.or.id/privkey.pem;
       ssl_protocols TLSv1.2 TLSv1.3;
       ssl_ciphers HIGH:!aNULL:!MD5;

       root /var/www/pogi-backend/public;
       index index.php;

       client_max_body_size 12M;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```
3. **Backup Rutin Database Otomatis**:
   Pasang cron job harian untuk mencadangkan database PostgreSQL ke cold storage:
   ```bash
   0 2 * * * pg_dump -U postgres pogi_skp_tracker | gzip > /backup/pogi_skp_$(date +\%F).sql.gz
   ```

---

## 6. Akun Resmi di Basis Data MySQL untuk Pengujian

Untuk kebutuhan pengujian, sistem menyediakan akun terdaftar resmi pada basis data MySQL dengan hash kata sandi Bcrypt terverifikasi mewakili 5 peran resmi sistem:

| Peran Resmi | ID / Email Resmi | Kata Sandi | Tujuan Akses |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `super@example.com` | `Super2026!Master` | Akses penuh seluruh modul & master data |
| **Pengurus Pusat** | `admin.pusat@pogi.or.id` | `AdminPusat2026!` | Monitoring nasional, SLA & performa cabang |
| **Admin POGI** | `sekretariat.pusat@pogi.or.id` | `POGI2026!Nasional` | Verifikasi SKP & kegiatan (tanpa modul performa) |
| **Admin Cabang (Jabar)** | `sekretariat.jabar@pogi.or.id` | `POGIJabar2026!` | Monitoring terbatas cabang Jawa Barat |
| **Dokter Sp.OG** | `cut.simatupang@pogi.or.id` | `DokterCut2026!` | Akses mandiri portofolio & capaian SKP dokter |
