# README.md — Sistem Monitoring & Manajemen SKP Nasional POGI
**Dikembangkan & Dikelola oleh PT Energi Cipta untuk Perkumpulan Obstetri dan Ginekologi Indonesia (POGI)**

---

## 1. Tentang Sistem
**Sistem Monitoring & Manajemen SKP Nasional POGI** adalah platform operasional B2B terpusat yang dirancang untuk membantu tim administrasi **PT Energi Cipta** dan pengurus **POGI** memantau, memvalidasi, serta mengelola pemenuhan Satuan Kredit Profesi (SKP) seluruh Dokter Spesialis Obstetri dan Ginekologi (Sp.OG) di Indonesia.

Sistem ini menjembatani proses pengumpulan berkas dari tingkat cabang/rumah sakit, penyimpanan arsip digital per anggota, pengingat otomatis melalui WhatsApp, pencatatan pembaruan ke portal SKP Kemenkes (Plataran Sehat), hingga pembuatan laporan bulanan (*Monthly Report*) sebagai dasar penagihan (*invoice*).

---

## 2. Fitur Utama Platform

* **Dashboard Eksekutif & Pemetaan Wilayah (38 Provinsi)**:
  * Ringkasan total dokter terpantau, persentase capaian SKP nasional, serta rasio status kelengkapan (*Complete* vs *Incomplete*).
  * **Peta Sebaran Kepatuhan Indonesia**: Visualisasi 38 provinsi dengan indikator warna **Merah** (Sangat Kurang), **Hijau** (Hampir Memenuhi), dan **Kuning** (Terpenuhi).
* **Monitoring Standar 3 Ranah SKP (Target 160 SKP / 5 Tahun)**:
  * Pelacakan otomatis proporsi wajib sesuai regulasi Kemenkes: **Pembelajaran (min. 45%)**, **Pelayanan (min. 35%)**, dan **Pengabdian Masyarakat (min. 5%)**.
  * Klasifikasi status kredit SKP: `Done`, `Submitted`, `Rejected`, dan `Not Submitted`.
* **Repositori Dokumen Anggota (*Document Vault*) & Deteksi Kekurangan**:
  * Penyimpanan terstruktur seluruh berkas sertifikat dan bukti praktik per nama dokter sebelum diunggah secara manual ke portal Kemenkes.
  * Penanda otomatis untuk anggota yang masih kekurangan berkas atau memiliki pengajuan ditolak.
* **Dual-Timestamp Audit Sinkronisasi**:
  * Menampilkan secara transparan waktu terakhir pembaruan data di **Dashboard Internal** (`Last Update Dashboard`) dan waktu terakhir pembaruan di **Portal Kemenkes** (`Last Update Kemenkes`).
* **Otomasi WhatsApp Blast & Pengingat Cutoff**:
  * Pengiriman pesan pengingat otomatis menjelang jadwal *cutoff* bulanan kepada anggota yang belum memenuhi target SKP/dokumen.
  * Fitur pengiriman WA Blast manual berbasis filter wilayah/cabang oleh admin.
* **Laporan Bulanan Otomatis & Arsip PDF (*Month-Year Picker*)**:
  * Pembuatan laporan otomatis setiap **tanggal 1 (Cutoff Bulanan)** yang tersimpan permanen dan dapat dipilih serta diunduh kembali berdasarkan bulan dan tahun sebagai lampiran *invoice*.

---

## 3. Struktur Dokumen Spesifikasi Proyek
Repositori dokumentasi proyek ini terdiri dari tiga dokumen utama:

| Dokumen | Deskripsi & Cakupan |
| :--- | :--- |
| **`README.md`** | Ikhtisar produk, fitur utama, struktur modul, dan panduan penyiapan lingkungan pengembangan. |
| **`ARCHITECTURE.md`** | Cetak biru arsitektur sistem, diagram alur data *end-to-end*, desain *worker* asinkron, dan skema database PostgreSQL. |
| **`RULES.md`** | Aturan bisnis B2B, formula perhitungan SKP 3 ranah, aturan warna peta, jadwal *cutoff* tanggal 1, kebijakan WA Blast, dan kepatuhan UU PDP. |

---

## 4. Rekomendasi Tech Stack & Standar Arsitektur

* **Backend & API**: **Laravel 11 (PHP 8.2+)** menerapkan **Clean Architecture (Domain, UseCases, Repositories, Slim Controllers)**, prinsip **SOLID**, dan **OOP Strict Types**.
* **Database Relasional**: **PostgreSQL 14+** (Skema normalisasi 3NF, **RFC 9562 UUIDv7**, Zero Full Table Scan Indexing, ekstensi `pg_trgm`, Covering Indexes, dan Materialized Views).
* **Frontend**: Laravel Blade + Tailwind CSS + Alpine.js (atau Next.js / Inertia.js React/Vue).
* **Queue & Scheduler**: Database / Redis Queue (Antrean WA Blast dengan *anti-ban jitter* 1.5 - 3s) dan Laravel Task Scheduler (Cutoff tanggal 1 jam 00:00 WIB).
* **Object Storage**: S3-compatible (AWS S3 / Cloudflare R2 / MinIO) untuk repositori berkas *Document Vault* dan arsip laporan PDF.
* **Keamanan & Kepatuhan**: Kepatuhan UU PDP No. 27/2022 (AES-256-GCM data sensitif, HMAC SHA-256 hash lookup, audit logging permanen, larangan integer auto-increment).

---

## 5. Standar Kinerja & Optimasi Query (*Zero Full Table Scan*)

1. **Covering Index (`INCLUDE`)**: Query agregasi dan penghitungan pemenuhan ranah SKP memanfaatkan *Index-Only Scan* (`idx_skp_entries_doc_period_domain`) tanpa membebani pembacaan tabel fisik (*zero heap read*).
2. **Pencegahan N+1 Query**: Wajib memuat relasi relasional menggunakan *Eager Loading* (`with(['branch', 'province', 'activePeriod'])`).
3. **Pencarian Cepat GIN Trigram**: Pencarian dokter berdasarkan nama, NPA POGI, atau nomor STR memanfaatkan ekstensi `pg_trgm` pada indeks GIN, menjamin respon di bawah 50ms.
4. **Isolasi Data Cabang Transparan**: Pembatasan data cabang pada peran Admin Cabang dieksekusi secara otomatis di level model melalui *Eloquent Global Scopes* (`BranchScope`).
5. **Paginasi Efisien**: Menggunakan *Cursor Pagination* (`cursorPaginate()`) dan pemrosesan bertahap (`chunkById()`) untuk menghindari beban memori pada volume data besar.

---

## 6. Struktur 5 Peran Resmi (*Role-Based Access Control / RBAC Matrix*)

1. **Super Admin**: Akses penuh (*Full-access*), konfigurasi master data (faskes, provinsi, cabang, kategori, event, pengguna), manajemen hak akses, dan audit trail log.
2. **Admin Pusat**: Memantau aktivitas operasional seluruh cabang, memonitor SLA dan performance kerja dari tim Admin POGI, serta export laporan nasional (audit/read mode pada data dokter).
3. **Admin POGI** *(Pengganti Verifikator)*: Melakukan verifikasi kesesuaian dokumen bukti fisik, validasi kredit SKP ranah, dan memantau dokter binaan di bawah penugasannya secara nasional.
4. **Admin Cabang**: Melakukan verifikasi dan monitoring berkas SKP, namun **terisolasi secara ketat khusus untuk dokter di wilayah cabangnya sendiri** (akses ke dokter cabang lain diblokir).
5. **User / Dokter**: Akses portal mandiri khusus untuk melihat portofolio SKP 5 tahun dan status resertifikasi dirinya sendiri.

---

## 7. Panduan Penyiapan Database & Eksekusi Seeder

```bash
# 1. Pastikan PostgreSQL 14+ aktif dan buat database
psql -U postgres -c "CREATE DATABASE pogi_skp_tracker WITH OWNER postgres ENCODING 'UTF8';"

# 2. Muat skema database produksi (3NF, Zero Full-Scan Index, UUIDv7)
psql -U postgres -d pogi_skp_tracker -f schema.sql

# 3. Muat data awal komprehensif (38 Provinsi, 38 Cabang, 91 RS, 12 User, 200 Dokter)
psql -U postgres -d pogi_skp_tracker -f seeds.sql
```