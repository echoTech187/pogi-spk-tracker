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

### B. Lapisan Aplikasi Inti (*Core Backend API*)
* **Teknologi**: Node.js (NestJS / Fastify) atau Python (FastAPI) dengan arsitektur berlapis (*Controller -> Service -> Repository*).
* **Modul Inti**:
  * **SKP Calculation Engine**: Menghitung otomatis persentase pemenuhan target $160\text{ SKP}/5\text{ tahun}$ dan validasi ambang batas minimal tiap ranah ($45\%$ Pembelajaran, $35\%$ Pelayanan, $5\%$ Pengabdian) serta pengelompokan status (`DONE`, `SUBMITTED`, `REJECTED`, `NOT_SUBMITTED`).
  * **Missing Document Detector**: Membandingkan dokumen yang diwajibkan per ranah dengan berkas yang sudah terunggah di *Document Vault* untuk menandai dokter yang memerlukan *follow-up*.
  * **Presigned URL File Handler**: Mengamankan proses unggah dan unduh sertifikat dokter langsung ke *Object Storage* tanpa membebani memori server API.

### C. Lapisan Pemrosesan Asinkron (*Background Workers & Scheduler*)
Dikelola menggunakan **Redis + BullMQ** untuk menjamin keandalan eksekusi tugas latar belakang:
1. **Monthly Cutoff & Snapshot Worker**:
   * Berjalan otomatis setiap **tanggal 1 pukul 00:00 WIB**.
   * Mengambil *snapshot* data SKP seluruh anggota, merender dokumen **Monthly Report PDF** (menggunakan Puppeteer / WeasyPrint), menyimpannya ke *Object Storage*, dan mendaftarkan metadata arsipnya ke tabel `monthly_reports`.
2. **WhatsApp Blast & Reminder Worker**:
   * Memproses antrean pengiriman pesan WA secara bertahap dengan pengaturan *rate-limit* (misal 10–15 pesan per menit) dan pencatatan status pengiriman (`QUEUED`, `SENT`, `DELIVERED`, `FAILED`).

---

## 3. Desain Skema Database Relasional (PostgreSQL 16+)
Berikut adalah entitas utama yang menyusun basis data sistem:

1. **`users` & `roles`**: Menyimpan akun pengguna sistem (`SUPER_ADMIN`, `ADMIN_OPERASIONAL`, `ADMIN_CABANG`, `VIEWER_POGI`) beserta hash kata sandi (Argon2id).
2. **`provinces` & `pogi_branches`**: Data master 38 provinsi di Indonesia dan struktur cabang POGI daerah beserta PIC cabang.
3. **`institutions`**: Data master rumah sakit dan fasilitas kesehatan tempat praktik dokter.
4. **`doctors` (Profil Anggota POGI)**:
   * Kolom utama: `id (UUIDv7)`, `npa_pogi`, `full_name`, `nik_encrypted`, `str_number`, `sip_number`, `sip_expiry_date`, `whatsapp_number`, `branch_id`, `province_id`, `institution_id`, `pic_admin_id`.
   * Kolom pelacakan sinkronisasi: `dashboard_updated_at`, `kemenkes_updated_at`, `plataran_sehat_username`, `plataran_sehat_pass_encrypted`.
5. **`skp_events` & `event_categories`**: Katalog kegiatan SKP (seminar, workshop, bakti sosial, dll.) beserta bobot nilai SKP dan ranahnya.
6. **`skp_entries` (Buku Log SKP Internal)**:
   * Mencatat setiap perolehan/pengajuan SKP per dokter: `doctor_id`, `event_id` (opsional), `domain` (`PEMBELAJARAN` | `PELAYANAN` | `PENGABDIAN`), `skp_points`, `activity_date`, `status` (`DONE` | `SUBMITTED` | `REJECTED` | `NOT_SUBMITTED`), `rejection_note`.
7. **`doctor_documents` (Repositori Berkas)**:
   * Metadata berkas digital milik dokter: `doctor_id`, `skp_entry_id`, `document_type`, `file_key` (path di S3/R2), `file_size`, `uploaded_by`, `verified_at`.
8. **`monthly_reports` (Arsip Laporan Cutoff)**:
   * Menyimpan arsip laporan per bulan: `report_period` (`YYYY-MM`), `cutoff_timestamp`, `total_doctors`, `total_skp_achieved`, `total_skp_target`, `pdf_file_key`, `generated_by`.
9. **`wa_blast_campaigns` & `wa_blast_logs`**: Menyimpan kampanye pengiriman WA Blast, template pesan, target filter, dan status pengiriman per nomor dokter.
10. **`audit_logs`**: Rekam jejak perubahan data sensitif dan aktivitas input/verifikasi oleh setiap admin.

---

## 4. Alur Data End-to-End (*Operational Workflow*)

1. **Pengumpulan & Verifikasi Awal**: Admin Cabang POGI atau anggota mengirimkan data dan bukti kegiatan SKP kepada tim Admin Energi Cipta.
2. **Input & Penyimpanan Berkas**: Admin Energi Cipta mengunggah berkas sertifikat/bukti ke *Document Vault* dokter di sistem internal. Sistem memperbarui `dashboard_updated_at` dan mengevaluasi kekurangan dokumen secara otomatis.
3. **Follow-Up & WA Blast**: Menjelang *cutoff* (atau sesuai jadwal *follow-up*), sistem/admin mengirimkan WA Blast otomatis kepada dokter dengan status dokumen kurang (`NOT_SUBMITTED` / `REJECTED`).
4. **Input Manual ke Portal Kemenkes**: Admin mengunduh berkas terverifikasi dari *Document Vault* internal, menginputkannya ke portal resmi SKP Kemenkes / Plataran Sehat milik anggota, lalu menekan tombol **"Tandai Sudah Update Kemenkes"** pada dashboard untuk mencatat `kemenkes_updated_at` dan mengubah status entri menjadi `SUBMITTED` / `DONE`.
5. **Cutoff Tanggal 1 & Penagihan**: Pada tanggal 1 setiap bulan, sistem mengunci data bulanan, menghasilkan file `Monthly_Report_POGI_YYYY_MM.pdf`, dan menyediakannya pada menu *Report* untuk diunduh sebagai lampiran penagihan (*invoice*).