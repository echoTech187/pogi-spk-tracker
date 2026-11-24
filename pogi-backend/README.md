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

## 4. Panduan Menjalankan Backend Laravel

1. Masuk ke direktori backend:
   ```bash
   cd pogi-backend
   ```
2. Pastikan file `.env` terkonfigurasi dengan database MySQL aktif:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pogi_skp_tracker
   DB_USERNAME=root
   DB_PASSWORD=root
   ```
3. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
4. Jalankan test otomatis:
   ```bash
   php artisan test
   ```

---

## 5. Akun Resmi di Basis Data MySQL untuk Pengujian

Untuk kebutuhan pengujian, sistem menyediakan akun terdaftar resmi pada basis data MySQL dengan hash kata sandi Bcrypt terverifikasi mewakili 5 peran resmi sistem:

| Peran Resmi | ID / Email Resmi | Kata Sandi | Tujuan Akses |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `super@example.com` | `Super2026!Master` | Akses penuh seluruh modul & master data |
| **Pengurus Pusat** | `admin.pusat@pogi.or.id` | `AdminPusat2026!` | Monitoring nasional, SLA & performa cabang |
| **Admin POGI** | `sekretariat.pusat@pogi.or.id` | `POGI2026!Nasional` | Verifikasi SKP & kegiatan (tanpa modul performa) |
| **Admin Cabang (Jabar)** | `sekretariat.jabar@pogi.or.id` | `POGIJabar2026!` | Monitoring terbatas cabang Jawa Barat |
| **Dokter Sp.OG** | `cut.simatupang@pogi.or.id` | `DokterCut2026!` | Akses mandiri portofolio & capaian SKP dokter |
