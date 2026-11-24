# ARCHITECTURE.md — Cetak Biru Arsitektur Sistem & Infrastruktur
**Sistem Monitoring & Manajemen SKP Nasional POGI (PT Energi Cipta)**

---

## 1. Ikhtisar Arsitektur Sistem
Sistem dirancang menggunakan pendekatan **Modular Monolith dengan Asynchronous Worker Pipeline** untuk menjaga kecepatan pengembangan, kemudahan pemeliharaan oleh tim kecil, namun tetap tangguh dalam menangani penyimpanan ribuan dokumen PDF/sertifikat, penjadwalan *cutoff* bulanan, pembuatan laporan PDF otomatis, serta pengiriman antrean pesan WhatsApp massal.

```
+-----------------------------------------------------------------------------------+
|                                CLIENT & PRESENTATION                              |
|  [ Web Console Admin Energi Cipta ]   [ Portal Pengurus POGI Pusat & Cabang ]     |
|           (Next.js / React + Tailwind CSS + Interactive Indonesia Map)            |
+-----------------------------------------+-----------------------------------------+
                                          | HTTPS / TLS 1.3 (JWT + RBAC)
+-----------------------------------------v-----------------------------------------+
|                                  API & CORE BACKEND                               |
|  +-----------------------+  +-----------------------+  +-----------------------+  |
|  |  Auth & RBAC Service  |  |  SKP & Member Engine  |  |  Report & Invoice Gen |  |
|  +-----------------------+  +-----------------------+  +-----------------------+  |
|  +-----------------------+  +-----------------------+  +-----------------------+  |
|  | Dual-Sync & Doc Vault |  |  Geo-Map Aggregator   |  |   Audit Trail Logger  |  |
|  +-----------------------+  +-----------------------+  +-----------------------+  |
+-------------------+---------------------+---------------------+-------------------+
                    |                     |                     |
        +-----------v-----------+   +-----v------+   +----------v-----------+
        |  PostgreSQL 16 (Main) |   | Redis 7    |   | S3-Compatible Vault  |
        |  - Members & SKP Logs |   | - Job Queue|   | - Member Certificates|
        |  - Monthly Snapshots  |   | - Cache    |   | - Archived PDF Report|
        +-----------------------+   +-----+------+   +----------------------+
                                          |
+-----------------------------------------v-----------------------------------------+
|                             ASYNCHRONOUS WORKER LAYER                             |
|  +-----------------------------+  +--------------------------------------------+  |
|  | Cutoff & PDF Report Worker  |  | WA Blast & Reminder Worker (BullMQ)        |  |
|  | (Cron: Tgl 1, 00:00 WIB)    |  | (Cron: H-7 & H-2 Cutoff + Manual Trigger)  |  |
|  +-----------------------------+  +----------------------+---------------------+  |
+----------------------------------------------------------+------------------------+
                                                           | Webhook / REST API
                                            +--------------v--------------+
                                            |   WhatsApp Gateway / Bot    |
                                            | (Official WABA / WA Engine) |
                                            +-----------------------------+
```

---

## 2. Komponen Utama Sistem

### A. Lapisan Presentasi (*Frontend Web Application*)
* **Teknologi**: Next.js (App Router) / React, TypeScript, Tailwind CSS, Lucide Icons, dan library peta vektor Indonesia (GeoJSON 38 Provinsi).
* **Desain Antarmuka**: Mengadopsi gaya *Clean Medical Dashboard* dengan dominasi latar `#F8FAFC`, kartu kontainer putih dengan sudut membulat (`rounded-2xl`), serta palet aksen utama hijau emerald (`#059669`) yang selaras dengan identitas organisasi POGI.
* **Modul Tampilan Utama**:
  1. **Executive & Operational Dashboard**: KPI Ringkasan (Total Dokter, Rata-rata Progres SKP, Status *Complete* vs *Incomplete*), Peta Sebaran Kepatuhan 38 Provinsi (Merah/Hijau/Kuning), dan Peringkat Cabang/Provinsi.
  2. **Direktori Anggota & Dual-Sync Monitor**: Daftar dokter Sp.OG lengkap dengan progres 3 ranah, indikator dokumen kurang, serta penanda waktu `Last Update Dashboard` vs `Last Update Kemenkes`.
  3. **Document Vault Per Anggota**: Tampilan folder digital per dokter (pengganti Google Drive manual) untuk menyimpan sertifikat Pembelajaran, bukti Pelayanan, dan surat Pengabdian yang siap diunduh admin saat input ke web Kemenkes.
  4. **Pusat WA Blast & Log Pengingat**: Antarmuka *filter* penerima pesan, pratinjau template pengingat SKP, serta riwayat status pengiriman pesan WhatsApp.
  5. **Arsip Laporan Bulanan (*Monthly Report & Invoice*)**: Pemilih periode bulan/tahun (*Month-Year Picker*) untuk melihat pratinjau laporan dan mengunduh file PDF resmi hasil *cutoff* tanggal 1.

### B. Lapisan Aplikasi Inti (*Clean Architecture Backend*)
* **Pendekatan Arsitektur**: **Clean Architecture (Hexagonal Architecture) & SOLID Principles** (Domain -> Application -> Infrastructure -> Presentation).
* **Framework**: Laravel 11 (PHP 8.2+) / Node.js NestJS.
* **Struktur Lapisan Bersih**:
  1. **Domain Layer (Core Rules)**: Domain Entities (`Doctor`, `SkpEntry`), Value Objects (`Nik`, `SkpCredit`), Enums (`SystemRole`, `SkpDomain`), dan Repository Interfaces (*Contracts*).
  2. **Application Layer (Use Cases)**: Action classes berskala tunggal (`VerifySkpEntryUseCase`, `RecalculateComplianceUseCase`, `GenerateMonthlyCutoffUseCase`) dan Data Transfer Objects (*Readonly DTOs*).
  3. **Infrastructure Layer**: Implementasi database PostgreSQL via Repositories (`EloquentDoctorRepository`), client WhatsApp Gateway anti-ban, dan S3 Document Vault.
  4. **Presentation Layer (MVC)**: *Slim Controllers* (hanya memanggil Use Case dan me-return DTO), Form Request Validation, dan Blade Views / ViewModels.

### C. Lapisan Pemrosesan Asinkron (*Background Workers & Scheduler*)
Dikelola menggunakan **Queue Worker & Scheduler** untuk menjamin keandalan eksekusi tugas latar belakang:
1. **Monthly Cutoff & Snapshot Worker**:
   * Berjalan otomatis setiap **tanggal 1 pukul 00:00 WIB**.
   * Mengambil *snapshot* data SKP seluruh anggota, merender dokumen **Monthly Report PDF**, menyimpannya ke *Object Storage*, dan mendaftarkan metadata arsipnya ke tabel `monthly_cutoff_snapshots`.
2. **WhatsApp Blast & Reminder Worker**:
   * Memproses antrean pengiriman pesan WA secara bertahap dengan pengaturan *rate-limit* dan *anti-ban jitter* (delay acak 1.5 - 3 detik) serta pencatatan status pengiriman (`QUEUED`, `SENDING`, `SENT`, `DELIVERED`, `FAILED`).

---

## 3. Desain Skema Database Relasional & Strategi Optimasi Query (PostgreSQL 14+)

### A. Entitas Utama Sistem (Normalisasi 3NF & RFC 9562 UUIDv7)
1. **`users`**: Akun pengguna sistem dengan 5 role resmi (`SUPER_ADMIN`, `ADMIN_PUSAT`, `ADMIN_POGI`, `ADMIN_CABANG`, `DOKTER`) dan password hash Argon2id.
2. **`provinces` & `pogi_branches`**: Master 38 provinsi di Indonesia (kode BPS) dan struktur cabang POGI daerah.
3. **`institutions`**: Master rumah sakit dan fasilitas kesehatan jejaring praktik.
4. **`doctors` (Master Anggota Sp.OG)**:
   * Kunci utama: `id (UUIDv7)`, `npa_pogi (UNIQUE)`, `full_name`.
   * UU PDP Enkripsi: `nik_encrypted (BYTEA AES-256)`, `nik_hash (SHA256 HMAC)`, `str_number`, `sip_number`, `sip_expiry_date`, `whatsapp_number`.
   * Relasi & Kepemilikan: `branch_id`, `province_id`, `primary_institution_id`, `assigned_pic_id (Admin POGI)`.
   * Sinkronisasi: `dashboard_updated_at`, `kemenkes_updated_at`.
5. **`doctor_skp_periods`**: Siklus masa berlaku 5 tahun dengan target 160 SKP (Target & Capaian per ranah: Pembelajaran 72, Pelayanan 56, Pengabdian 8, Fleksibilitas 24).
6. **`event_categories` & `skp_events`**: Katalog kegiatan SKP terstandar Kemenkes Plataran Sehat.
7. **`skp_entries`**: Buku log transaksi kredit SKP per ranah (`DONE`, `SUBMITTED`, `REJECTED`, `NOT_SUBMITTED`).
8. **`doctor_documents` (Document Vault)**: Repositori berkas sertifikat dan bukti praktik lengkap dengan checksum SHA-256.
9. **`monthly_cutoff_snapshots` & `monthly_doctor_cutoff_details`**: Snapshot arsip permanen laporan bulanan dan lampiran *invoice*.
10. **`wa_blast_campaigns` & `wa_message_queue`**: Antrean pesan broadcast WhatsApp dengan jitter anti-ban.
11. **`audit_logs` & `verifier_performance_logs`**: Rekam jejak audit kepatuhan UU PDP dan log SLA verifikasi Admin POGI.

### B. Strategi Optimasi Query Database (*Zero Full Table Scan Architecture*)
Untuk memastikan respon server tetap di bawah 50ms saat menangani puluhan ribu dokter dan ratusan ribu entri SKP, sistem menerapkan arsitektur indeks berlapis:

1. **Covering Index dengan Kolom Proyeksi (`INCLUDE`)**:
   * Indeks `idx_skp_entries_doc_period_domain` pada tabel `skp_entries`:
     ```sql
     CREATE INDEX idx_skp_entries_doc_period_domain 
     ON skp_entries(doctor_id, period_id, domain, status) 
     INCLUDE (credit_amount);
     ```
     Menghasilkan **Index-Only Scan** saat menghitung total capaian per ranah, database tidak perlu membaca blok tabel utama (*heap read = 0*).
2. **Pencarian Fuzzy Cepat Berbasis GIN Trigram (`pg_trgm`)**:
   * Indeks `idx_doctors_trgm_search` pada `doctors(full_name gin_trgm_ops, npa_pogi gin_trgm_ops, str_number gin_trgm_ops)`:
     Mendukung fitur pencarian autocomplete dokter secara instan tanpa memicu sequential table scan.
3. **Composite Partial Indexes untuk Isolasi & Dashboard**:
   * `idx_doctors_branch_status` pada `(branch_id, status)` memfilter dokter cabang secara instan.
   * `idx_doctors_pic_status` pada `(assigned_pic_id, status)` mempercepat dashboard kerja masing-masing Admin POGI.
   * `idx_wa_queue_pending` pada `(scheduled_at) WHERE status = 'QUEUED'` membuat worker queue hanya membaca pesan yang belum dikirim.
4. **Pencegahan N+1 Query di Lapisan Repository**:
   * Setiap query pemanggilan dokter wajib memuat relasi kritis sekaligus menggunakan Eager Loading:
     `Doctor::with(['branch:id,name', 'province:id,name', 'activePeriod'])->select([...])`.
5. **Materialized View Caching untuk Peta Nasional**:
   * Menghindari kalkulasi agregasi berat (`COUNT`, `SUM`, `GROUP BY`) saat user membuka peta 38 provinsi melalui `mv_province_compliance_summary`. Dilakukan refresh cepat dengan `REFRESH MATERIALIZED VIEW CONCURRENTLY`.

---

## 4. Alur Data End-to-End (*Operational Workflow*)

1. **Pengumpulan & Verifikasi Awal**: Admin Cabang POGI atau anggota mengirimkan data dan bukti kegiatan SKP kepada tim Admin Energi Cipta.
2. **Input & Penyimpanan Berkas**: Admin Energi Cipta mengunggah berkas sertifikat/bukti ke *Document Vault* dokter di sistem internal. Sistem memperbarui `dashboard_updated_at` dan mengevaluasi kekurangan dokumen secara otomatis.
3. **Follow-Up & WA Blast**: Menjelang *cutoff* (atau sesuai jadwal *follow-up*), sistem/admin mengirimkan WA Blast otomatis kepada dokter dengan status dokumen kurang (`NOT_SUBMITTED` / `REJECTED`).
4. **Input Manual ke Portal Kemenkes**: Admin mengunduh berkas terverifikasi dari *Document Vault* internal, menginputkannya ke portal resmi SKP Kemenkes / Plataran Sehat milik anggota, lalu menekan tombol **"Tandai Sudah Update Kemenkes"** pada dashboard untuk mencatat `kemenkes_updated_at` dan mengubah status entri menjadi `SUBMITTED` / `DONE`.
5. **Cutoff Tanggal 1 & Penagihan**: Pada tanggal 1 setiap bulan, sistem mengunci data bulanan, menghasilkan file `Monthly_Report_POGI_YYYY_MM.pdf`, dan menyediakannya pada menu *Report* untuk diunduh sebagai lampiran penagihan (*invoice*).