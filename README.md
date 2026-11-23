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

## 4. Rekomendasi Tech Stack

* **Frontend**: Next.js 14+ (React, TypeScript), Tailwind CSS, Recharts / Chart.js, React-Simple-Maps (GeoJSON Indonesia 38 Provinsi).
* **Backend API**: Node.js (NestJS / Express) dengan TypeScript & Prisma ORM (atau Python FastAPI).
* **Database & Cache**: PostgreSQL 16+ dan Redis 7+ (untuk antrean *job* BullMQ).
* **Object Storage**: AWS S3 / Cloudflare R2 / MinIO (untuk penyimpanan sertifikat anggota dan arsip laporan PDF).
* **PDF Generator**: Puppeteer / Playwright (merender template HTML laporan bulanan ke PDF presisi tinggi).
* **WhatsApp Integration**: WhatsApp Business API (WABA) / WA Gateway Resmi.

---

## 5. Panduan Instalasi & Pengembangan Lokal

### Prasyarat Sistem
* Node.js `v20.x` atau lebih baru
* PostgreSQL `v16` atau lebih baru
* Redis `v7` atau lebih baru

### Langkah Penyiapan
```bash
# 1. Kloning repositori proyek
git clone https://github.com/energicipta/pogi-skp-monitoring.git
cd pogi-skp-monitoring

# 2. Instalasi dependensi
npm install

# 3. Salin konfigurasi environment
cp .env.example .env
```

### Konfigurasi Variabel Lingkungan (`.env`)
```env
# Database & Redis
DATABASE_URL="postgresql://postgres:password@localhost:5432/pogi_skp_db?schema=public"
REDIS_URL="redis://localhost:6379"

# Keamanan & Enkripsi (UU PDP Compliance)
JWT_SECRET="ganti-dengan-secret-key-minimal-32-karakter"
ENCRYPTION_MASTER_KEY="ganti-dengan-32-byte-hex-key-aes256"

# Object Storage (S3 / Cloudflare R2 / MinIO)
S3_ENDPOINT="https://s3.ap-southeast-1.amazonaws.com"
S3_BUCKET_NAME="pogi-skp-vault"
S3_ACCESS_KEY="your-access-key"
S3_SECRET_KEY="your-secret-key"

# WhatsApp Gateway & Jadwal Cutoff
WA_GATEWAY_URL="https://api.wagateway.energicipta.id/v1"
WA_API_TOKEN="your-wa-token"
CUTOFF_CRON_SCHEDULE="0 0 1 * *"
```

### Migrasi Database & Menjalankan Server
```bash
# Jalankan migrasi skema database dan data awal (38 Provinsi & Kategori SKP)
npx prisma migrate dev
npx prisma db seed

# Jalankan server pengembangan (Frontend & Backend)
npm run dev
```

---

## 6. Peran Pengguna (*Role-Based Access Control*)

1. **Super Admin (Energi Cipta)**: Akses penuh ke seluruh konfigurasi sistem, manajemen pengguna, log audit, *document vault*, WA Blast, dan arsip laporan.
2. **Admin Operasional (Tim Input Energi Cipta)**: Mengelola data anggota dokter, mengunggah berkas ke *vault*, mencatat status update portal Kemenkes, dan memicu pengingat WhatsApp.
3. **Admin Cabang POGI**: Melihat rekap kepatuhan anggota di cabangnya dan mengunggah berkas kolektif dari daerah.
4. **Pengurus Pusat POGI (*Viewer*)**: Memantau dashboard nasional, peta sebaran 38 provinsi, serta mengunduh laporan bulanan (*Monthly Report PDF*) untuk validasi *invoice*.