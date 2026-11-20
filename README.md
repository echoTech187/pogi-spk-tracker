# POGI SKP Tracker - Platform Pemantauan 160 SKP Dokter Spesialis Obgyn

**Perkumpulan Obstetri dan Ginekologi Indonesia (PP POGI)**  
*Sistem Nasional Tata Kelola Kepatuhan Kredit Pendidikan Berkelanjutan (P2KB) & Pemenuhan Standar 160 SKP / 5 Tahun*

---

## 1. Latar Belakang & Ringkasan Kebutuhan Klien

Aplikasi ini dibangun khusus untuk kebutuhan **Pengurus Pusat & Pengurus Cabang POGI** di seluruh Indonesia dalam mengelola, memantau, dan menindaklanjuti kepatuhan perolehan Satuan Kredit Profesi (SKP) seluruh **Dokter Spesialis Obstetri & Ginekologi (Sp.OG)**.

### Aturan Bisnis Mutlak (Business Rules):
1. **Target Mandatori 160 SKP / 5 Tahun**:
   * Setiap dokter spesialis Obgyn wajib mengumpulkan minimal **160 SKP** dalam siklus perpanjangan STR 5 tahunan (rata-rata 32 SKP / tahun, atau 8 SKP / kuartal).
2. **Kewajiban 3 Subbidang SKP**:
   * **1. Pelayanan**: Praktik klinis rawat inap/jalan, tindakan bedah persalinan/sectio caesarea, jaga IGD PONEK maternal. (Standar acuan ~80 SKP).
   * **2. Pengabdian**: Bakti sosial pemeriksaan IVA, skrining kanker serviks masal, penyuluhan kesehatan reproduksi. (Standar acuan ~30 SKP).
   * **3. Pembelajaran**: Pertemuan Ilmiah Tahunan (PIT) POGI, simposium ilmiah, workshop hands-on USG fetomaternal, webinar terakreditasi. (Standar acuan ~50 SKP).
3. **Model Bisnis Murni B2B**:
   * Sistem mengelola dokter yang merupakan anggota resmi POGI terdaftar.
   * Tim Pengelola Data bertugas memantau dan melakukan **Follow-Up (FU)** ke **Rumah Sakit jejaring** atau ke **Sekretariat Pengurus Cabang POGI di 38 Provinsi**.

---

## 2. Struktur Modul & Halaman Sistem

Sistem dirancang modular menggunakan arsitektur berkas terpisah berbasis **Tailwind CSS & JavaScript Vanilla Modern**:

| Berkas Halaman | Fungsi & Komponen Utama |
| :--- | :--- |
| **`index.html`** | Halaman autentikasi/login resmi POGI, dilengkapi shortcut 1-klik masuk demo untuk Admin Pusat, Admin Cabang Jabar, dan PIC RS Jejaring. |
| **`dashboard.html`** | Dashboard monitoring nasional: Metrik Total Dokter (200), Rata-rata Progress Q4 (43%), Complete (42), Incomplete (158), pemantauan 3 Subbidang, serta tabel peringkat provinsi terendah (Lowest) vs tertinggi (Highest). |
| **`doctors.html`** | Direktori dokter Sp.OG dengan filter Kuartal (Q4 2026), Provinsi, dan Status. Menampilkan progress bar SKP, pending credits, rincian 3 subbidang, dan modal verifikasi logbook. |
| **`provinces.html`** | Monitoring 38 Provinsi di Indonesia lengkap dengan persentase kepatuhan, dokter Complete vs Incomplete, filter sembunyikan provinsi kosong, serta tombol aksi kirim FU cabang. |
| **`events.html`** | Katalog kegiatan dan event ilmiah/baksos ber-SKP POGI (PIT POGI, Workshop USG, Baksos Serviks), filter subbidang, dan formulir tambah event baru. |
| **`activity.html`** | Log aktivitas dan jejak audit tim data: riwayat verifikasi kredit, integrasi SATUSEHAT SDMK Kemenkes, pengiriman notifikasi FU. |
| **`reports.html`** | Laporan eksekutif dengan 4 grafik Chart.js interaktif (Target vs Realisasi per Bulan, per Tipe SKP, Komposisi Donut, Status Stacked Bar) sesuai data PDF resmi, dilengkapi fitur cetak PDF (`print:hidden` / print-ready). |
| **`manage.html`** | Master data management dengan tab: Doctor Master, Institution (RS Jejaring), SKP Event Master, Event Category, dan User RBAC. |

---

## 3. Direktori Berkas

```
pogi-spk-tracker/
├── assets/
│   └── img/
│       ├── pogi_logo.jpg       # Logo Resmi POGI
│       └── pogi_logo.png
├── css/
│   └── style.css               # Global stylesheet, font Manrope/Public Sans, progress bar
├── js/
│   ├── pogi_state.js           # State management & local storage mock database
│   └── navigation.js           # Shared sidebar drawer, mobile responsive, toast notifications
├── index.html                  # Halaman Login
├── dashboard.html              # Dashboard Monitoring Nasional
├── doctors.html                # Direktori Dokter Obgyn (160 SKP)
├── provinces.html              # Monitoring 38 Provinsi
├── events.html                 # Katalog Event SKP
├── activity.html               # Log Audit & Aktivitas Tim Data
├── reports.html                # Laporan & Grafik Chart.js
├── manage.html                 # Master Data Organisasi
└── README.md                   # Dokumentasi Resmi Proyek
```

---

## 4. Cara Menjalankan Sistem

1. Buka folder `c:\Users\Pongo\Downloads\pogi-spk-tracker\` di File Explorer atau browser.
2. Klik ganda pada berkas **`index.html`** untuk membuka halaman login.
3. Gunakan tombol **1-Klik Masuk** (Admin Data Pusat) lalu klik **Masuk ke Konsol SKP POGI**.
4. Seluruh fitur navigasi, filter pencarian, pengiriman follow-up, verifikasi kredit, grafik analisis, dan responsive mobile/tablet telah aktif dan terhubung dengan data mock POGI.
