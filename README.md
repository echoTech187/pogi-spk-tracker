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

Sesuai arahan operasional manajemen PP POGI & hasil evaluasi alur kerja:

| No | Peran (*System Role*) | Personil Utama | Deskripsi & Batas Wewenang |
|:---:|:---|:---|:---|
| **1** | **Super Admin** | Bu Dian, Bp. Ade, Bp. Ircham | Akses penuh (*Full-access*), konfigurasi master data, **wewenang eksklusif menentukan & mengalihkan Admin PIC pengelola dokter** (`assigned_pic_id`), penyeimbang beban (*workload rebalancing*), laporan bulanan resmi, dan audit trail log. |
| **2** | **Admin POGI** *(Data Entry & Verifikator)* | Anna, Vivi, Lily, Dewi Lestari, Fikri Ramadhan | Penginputan dan pembaruan data dari Kemenkes ke dashboard POGI, verifikasi berkas SKP, dan monitoring dokter binaan. Setiap klik tombol **"Akses Portal Kemenkes"** langsung mengarahkan ke portal resmi **SatuSehat SDMK Kemenkes RI (`https://satusehat.kemkes.go.id/sdmk/login`)** dan otomatis tercatat sebagai riwayat kerja (*auto-logging* SLA). **Tidak dapat mengubah PIC dokter**. |
| **3** | **POGI Pusat** | dr. Ulul, Bu Ratna | Pemantauan eksekutif nasional (*Executive Read-Only Monitoring*). **Hanya bisa cek saja**, memiliki akses pemantauan ke **seluruh wilayah cabang (38 provinsi)**, namun tidak dapat mengedit data atau mengubah PIC. |
| **4** | **Admin Cabang** | Sekretariat Cabang Daerah (dr. Firman, Sp.OG, dll.) | Pemantauan cabang daerah (*Branch Read-Only Monitoring*). **Hanya bisa cek saja**, terisolasi ketat **hanya untuk dokter di wilayah cabangnya sendiri** (*multi-tenant scope*). Tidak dapat mengedit data atau mengubah PIC. |
| **5** | **User / Dokter** | Anggota Sp.OG (dr. Cut Simatupang, dll.) | Akses portal mandiri khusus untuk melihat portofolio SKP 5 tahun dan status resertifikasi dirinya sendiri. |

---

## 4. Struktur Dokumen Acuan Proyek

| Dokumen | Deskripsi & Cakupan |
|:---|:---|
| **[`README.md`](file:///c:/Users/Pongo/Downloads/pogi-spk-tracker/README.md)** | Ikhtisar produk, fitur utama, standar RBAC 5 role, dan **panduan step-by-step implementasi produksi**. |
| **[`RULES.md`](file:///c:/Users/Pongo/Downloads/pogi-spk-tracker/RULES.md)** | Seluruh aturan bisnis operasional B2B, formula perhitungan SKP 160, siklus cutoff tanggal 1, jadwal WA Blast, kode warna peta, matriks RBAC, dan kepatuhan UU PDP No. 27/2022. |
| **[`ARCHITECTURE.md`](file:///c:/Users/Pongo/Downloads/pogi-spk-tracker/ARCHITECTURE.md)** | Cetak biru Clean Architecture, diagram **ERD lengkap 18 tabel**, **DFD Level 0 & Level 1**, 4 Diagram Alur Kerja (*Workflows*), Batasan Sistem, dan Strategi Indeks *Zero Full-Scan*. |

---

## 5. Panduan Implementasi Step-by-Step (Persiapan s.d. Release Production)

Berikut adalah panduan operasional teknis tahap demi tahap untuk membangun, mengonfigurasi, dan merilis sistem ini ke lingkungan produksi menggunakan stack **Laravel 11 & MySQL 8+ / PostgreSQL 14+**:

### Fase 1: Prasyarat Sistem & Kesiapan Environment

Pastikan server atau mesin pengembangan Anda telah memenuhi spesifikasi minimum berikut:
* **PHP**: Versi `8.2.x` atau `8.3.x` (Ekstensi wajib: `pdo_mysql` / `pdo_pgsql`, `openssl`, `mbstring`, `curl`, `bcmath`, `fileinfo`, `gd`, `intl`).
* **Composer**: Versi `2.7.x` atau lebih baru.
* **Database**: MySQL 8.0+ atau PostgreSQL 14.x+.
* **Redis**: Versi `7.x` (untuk antrean pesan background *Queue Worker* dan cache).
* **Node.js & NPM**: Node `v20.x` LTS untuk kompilasi aset frontend.

---

### Fase 2: Inisialisasi Proyek Laravel 11 & Konfigurasi Lingkungan

1. Clone repositori & masuk ke direktori proyek backend:
   ```bash
   cd pogi-backend
   composer install
   npm install && npm run build
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
   APP_URL=http://127.0.0.1:8000

   # Koneksi Basis Data
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pogi_skp_tracker
   DB_USERNAME=root
   DB_PASSWORD=root
   ```

---

### Fase 3: Menjalankan Pengujian Otomatis

Seluruh pengujian unit dan pengujian fitur otorisasi RBAC dapat dijalankan menggunakan Artisan:
```bash
php artisan test
```

---

## 6. Akun Resmi di Basis Data MySQL untuk Pengujian

Untuk kebutuhan pengujian, sistem menyediakan akun terdaftar resmi pada basis data MySQL dengan hash kata sandi Bcrypt terverifikasi mewakili 5 peran resmi sistem:

| Peran Resmi | Personil & Email Resmi | Kata Sandi | Tujuan & Batasan Akses |
| :--- | :--- | :--- | :--- |
| **Super Admin** | Dian (`super@example.com`), Bp. Ade (`ade@pogi.or.id`), Bp. Ircham (`ircham@pogi.or.id`) | `Super2026!Master` | Akses penuh, alihkan PIC dokter, rebalance beban, master data |
| **Admin POGI** | Anna (`sekretariat.pusat@pogi.or.id`), Vivi (`vivi@pogi.or.id`), Lily (`lily@pogi.or.id`) | `POGI2026!Nasional` | Input & update data, verifikasi berkas, auto-log Kemenkes, pantau SLA pribadi |
| **POGI Pusat** | dr. Ulul (`admin.pusat@pogi.or.id`), Bu Ratna (`bu.ratna@pogi.or.id`) | `AdminPusat2026!` | Monitoring eksekutif 38 provinsi nasional (**Hanya bisa cek saja**) |
| **Admin Cabang** | dr. Firman, Sp.OG (`sekretariat.jabar@pogi.or.id`) | `POGIJabar2026!` | Monitoring terbatas cabang Jawa Barat (**Hanya bisa cek saja**) |
| **Dokter Sp.OG** | dr. Cut Simatupang, Sp.OG (`cut.simatupang@pogi.or.id`) | `DokterCut2026!` | Akses mandiri portofolio & capaian SKP dokter |
