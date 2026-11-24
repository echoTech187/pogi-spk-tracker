# RULES.md — Aturan Bisnis, Logika Sistem & Kepatuhan Regulasi
**Sistem Monitoring & Manajemen SKP Nasional POGI**
*Dikelola secara eksklusif oleh PT Energi Cipta untuk Pengurus Pusat & Cabang Perkumpulan Obstetri dan Ginekologi Indonesia (POGI)*

---

## 1. Model Bisnis & Ruang Lingkup Operasional (B2B)

1. **Model Layanan B2B Eksklusif**:
   * Sistem ini beroperasi murni sebagai platform **B2B (*Business-to-Business*)** antara **PT Energi Cipta** (penyedia infrastruktur teknologi & operasional data) dan **POGI** (Perkumpulan Obstetri dan Ginekologi Indonesia) beserta 38 cabang daerah di seluruh Indonesia.
   * Sistem tidak melayani pendaftaran ritel langsung dari dokter umum atau pihak luar (*non-B2C*). Seluruh dokter yang terdaftar adalah anggota resmi POGI pemegang gelar Spesialis Obstetri dan Ginekologi (Sp.OG).
2. **Batasan Integrasi Kemenkes (Non-API / Assisted Sync) & Kebijakan Berkas Bukti**:
   * Portal resmi SKP Kemenkes (*Plataran Sehat*) **tidak menyediakan API publik** untuk penarikan maupun pengunggahan data secara otomatis.
   * Seluruh berkas bukti fisik SKP (sertifikat simposium, logbook operasi/tindakan, surat tugas pengabdian) **wajib disertakan melalui URL / Tautan Google Drive (MANDATORI)** sebelum diverifikasi oleh tim verifikator dan diinput ke portal Kemenkes oleh tim Admin POGI / tim data.
   * **Kebijakan Anti-Storage Bloat (Pencegahan Pembengkakan Storage Server)**:
     - Pengunggahan file fisik biner langsung ke server adalah **OPSIONAL**.
     - Input **URL Google Drive adalah MANDATORI (WAJIB)**.
     - Kebijakan ini diterapkan karena kekhawatiran pihak manajemen dan *stakeholder* terhadap membengkaknya kapasitas disk server dan tingginya biaya *bandwidth / storage hosting* jika menampung puluhan ribu berkas biner ribuan dokter spesialis obgyn di seluruh Indonesia.
   * Sistem wajib menyediakan mekanisme deteksi kekurangan dokumen (*missing document detector*) dan pelacakan *dual-timestamp* yang memisahkan waktu pembaruan internal dan waktu sinkronisasi Kemenkes.

---

## 2. Regulasi & Logika Perhitungan SKP Kemenkes

### 2.1 Landasan Hukum
Penilaian kredit Satuan Kredit Profesi (SKP) mengacu secara ketat pada:
1. **Keputusan Menteri Kesehatan RI No. HK.01.07/MENKES/1561/2024** tentang Petunjuk Teknis Pengelolaan Satuan Kredit Profesi Tenaga Medis dan Tenaga Kesehatan.
2. Pedoman teknis penyempurna **KMK HK.01.07/MENKES/1111/2026**.

### 2.2 Target Wajib Siklus 5 Tahun
Setiap dokter Sp.OG memiliki siklus pemenuhan SKP selama **5 tahun** (mengikuti masa berlaku STR/SIP):
* **Target Total Wajib**: Minimal **160 SKP**.
* **Target Pemantauan Tahunan**: Rata-rata **32 SKP / tahun**.
* **Target Pemantauan Kuartalan**: Rata-rata **8 SKP / kuartal**.

### 2.3 Formula Pembagian 3 Ranah Wajib + Ranah Fleksibilitas
Total 160 SKP dibagi ke dalam ambang batas persentase mutlak:

$$\text{Total Target} = 160 \text{ SKP} = \text{Pembelajaran (72)} + \text{Pelayanan (56)} + \text{Pengabdian (8)} + \text{Fleksibilitas (24)}$$

| Ranah Kompetensi | Persentase Minimal | Beban SKP Minimal | Cakupan Kegiatan yang Diakui |
|:---|:---:|:---:|:---|
| **Ranah Pembelajaran** | **$\ge 45\%$** | **$72\text{ SKP}$** | Simposium ilmiah, workshop USG fetomaternal, seminar, webinar terakreditasi, kursus Plataran Sehat. |
| **Ranah Pelayanan** | **$\ge 35\%$** | **$56\text{ SKP}$** | Praktik klinis rawat jalan, tindakan operasi sectio caesarea, penanganan persalinan patologis, tindakan ginekologi. |
| **Ranah Pengabdian Masyarakat** | **$\ge 5\%$** | **$8\text{ SKP}$** | Bakti sosial penanganan stunting, penyuluhan kanker serviks, penugasan bencana, kepengurusan aktif organisasi profesi. |
| **Ranah Fleksibilitas** | **$\le 15\%$** | **$24\text{ SKP}$** | Bebas dipenuhi dari kelebihan kredit di salah satu atau kombinasi ketiga ranah di atas untuk menggenapkan total 160 SKP. |

### 2.4 Formula Evaluasi Status Kepatuhan Dokter
Sistem mengevaluasi status kepatuhan dokter secara otomatis melalui aturan berjenjang:
1. **Status `COMPLETE`**:
   * $\text{Total SKP Terverifikasi} \ge 160.0$, **DAN**
   * $\text{SKP Pembelajaran} \ge 72.0$, **DAN**
   * $\text{SKP Pelayanan} \ge 56.0$, **DAN**
   * $\text{SKP Pengabdian} \ge 8.0$.
2. **Status `INCOMPLETE`**:
   * Total SKP $< 160.0$ **ATAU** salah satu ranah wajib belum memenuhi nilai minimal, meskipun total kumulatif telah melampaui 160 SKP.
3. **Peringatan Dini Kepatuhan Berdasarkan Sisa Masa Berlaku SIP**:
   * **`AMAN`**: Sisa masa berlaku SIP $> 12$ bulan atau status telah `COMPLETE`.
   * **`WASPADA`**: Sisa masa berlaku SIP antara $6 - 12$ bulan dengan status masih `INCOMPLETE`.
   * **`KRITIS`**: Sisa masa berlaku SIP $< 6$ bulan dan status masih `INCOMPLETE` (otomatis masuk prioritas tertinggi WA Blast dan pengawalan Admin POGI).

### 2.5 Siklus Hidup Status Entri SKP (*SKP Entry Lifecycle*)
Setiap entri kegiatan SKP di buku log melewati tahapan status mutlak:
1. `NOT_SUBMITTED`: Dokumen bukti telah diunggah oleh dokter/cabang tetapi berkas belum lengkap atau belum diperiksa.
2. `SUBMITTED`: Dokumen telah diverifikasi internal dan siap/sedang diajukan ke portal Kemenkes.
3. `DONE`: Kredit SKP telah resmi disetujui dan tercatat pada buku log Plataran Sehat Kemenkes (nilai kredit masuk ke akumulasi kelulusan).
4. `REJECTED`: Pengajuan ditolak oleh verifikator (karena cap faskes tidak terbaca, barcode sertifikat tidak valid, atau kegiatan tidak linier) dan wajib mencantumkan alasan penolakan pada `rejection_reason`.

---

## 3. Logika Bisnis Cutoff Bulanan & Arsip Laporan (Invoice Basis)

1. **Jadwal Cutoff Tetap**:
   * *Cutoff* data bulanan dilakukan secara otomatis tepat pada **hari pertama setiap bulan pukul 00:00:00 WIB** (misal: 1 Desember, 1 Januari).
2. **Mekanisme Penguncian (*Snapshot Locking*)**:
   * Tepat pada saat cutoff, sistem mengeksekusi *snapshot query* yang mengunci:
     - Jumlah total dokter terpantau.
     - Jumlah dokter berstatus `COMPLETE` dan `INCOMPLETE`.
     - Akumulasi kredit terverifikasi (`total_skp_verified`) dan tertunda (`total_skp_pending`).
     - Detail capaian masing-masing dari 200 dokter pada saat cutoff.
   * Data ini disimpan secara permanen pada tabel `monthly_cutoff_snapshots` dan `monthly_doctor_cutoff_details` dengan status `is_locked = TRUE`.
3. **Penerbitan Laporan PDF & Dasar Penagihan (Invoice)**:
   * Worker sistem merender dokumen resmi `Monthly_Report_POGI_YYYY_MM.pdf`.
   * Laporan dihitung dengan checksum SHA-256 dan disimpan pada *Object Storage* yang dapat diunduh kembali kapan saja melalui *Month-Year Picker* oleh Super Admin dan Admin Pusat sebagai lampiran penagihan operasional ke PP POGI.

---

## 4. Logika Bisnis Notifikasi & WhatsApp Blast Automation

1. **Pemicu Jadwal Otomatis (*Automated Triggers*)**:
   * **H-7 Cutoff (Tanggal 24 setiap bulan)**: Peringatan dini kepada dokter berstatus `INCOMPLETE` atau memiliki berkas berstatus `NOT_SUBMITTED` / `REJECTED`.
   * **H-2 Cutoff (Tanggal 28/29 setiap bulan)**: Peringatan darurat pemenuhan kuartal berjalan.
2. **Mekanisme Antrean & Anti-Ban Jitter (*Message Queue Protection*)**:
   * Pengiriman pesan massal dilarang keras ditembakkan secara bersamaan (*burst spam*).
   * Seluruh pesan dimasukkan ke antrean `wa_message_queue` dengan status `QUEUED`.
   * Worker pengiriman wajib menerapkan **jeda acak (*anti-ban jitter*) antara 1.500 ms hingga 3.000 ms** per pesan.
   * Tingkat pengiriman dibatasi maksimal **15 pesan per menit** per nomor pengirim untuk menjaga reputasi nomor WhatsApp gateway resmi.
3. **Template Pesan Dinamis**:
   * Pesan wajib menyertakan parameter dinamis: nama lengkap dokter, sisa kekurangan SKP per ranah, deadline tanggal cutoff, serta tautan portal anggota.

---

## 5. Logika Visualisasi Peta Kepatuhan Wilayah (38 Provinsi)

Peta sebaran nasional 38 provinsi dihitung berdasarkan agregasi persentase pemenuhan rata-rata anggota di provinsi terkait:

$$\text{Capaian Provinsi (\%)} = \left( \frac{\sum \text{SKP Terverifikasi Seluruh Anggota di Provinsi}}{\sum \text{Target SKP Berjalan Seluruh Anggota di Provinsi}} \right) \times 100\%$$

### Kode Warna Baku Sesuai Standar Operasional POGI:
* **Kuning (`#EAB308`) — Terpenuhi / Aman**: Capaian wilayah **$\ge 85\%$**. Provinsi berada di jalur kepatuhan yang aman.
* **Hijau (`#16A34A`) — Hampir Memenuhi / Waspada**: Capaian wilayah **$50\% \le \text{Capaian} < 85\%$**. Memerlukan pemantauan berkala.
* **Merah (`#DC2626`) — Sangat Kurang / Kritis**: Capaian wilayah **$< 50\%$**. Memerlukan intervensi langsung dan pemberitahuan massal ke pengurus cabang setempat.

### Tema Brand & Palet Warna Default Sistem (Teal Standard):
Sesuai arahan klien, palet warna baku utama sistem POGI SKP Tracker menggunakan standar Teal / Turquoise Cyan:
* **Warna Utama Default (*Primary Brand*)**: `#16b3ac` (Teal / Turquoise Cyan)
* **Warna Interaksi Hover / Aktif (*Hover / Focus*)**: `#129993`
* **Warna Aksen Gelap (*Dark Slate-Teal Gradient*)**: `#0a4c49` hingga `#052625`
* **Warna Latar Terang / Tint (*Light Tint Background*)**: `#e8f8f7`
* **Warna Aksen Emas (*POGI Gold Accent*)**: `#c29b38`

---

## 6. Logika Kinerja & SLA Admin POGI (Verifikator)

Untuk memastikan berkas dokter tidak tertahan lama, sistem memonitor kinerja tim Admin POGI:
1. **Target SLA Turnaround Time**: Verifikasi berkas maksimal **$\le 24$ jam kerja** sejak dokumen diunggah.
2. **Formula Skor Kinerja Keseluruhan (*Overall Performance Score*)**:
   $$\text{Score} = (\text{SLA Score} \times 0.35) + (\text{Accuracy Rate} \times 0.30) + (\text{Volume Binaan} \times 0.20) + (\text{Completion Rate} \times 0.15)$$
3. **Klasifikasi Kategori Kinerja**:
   * **Top Performer** ($\text{Score} \ge 90$): SLA $< 12$ jam, tingkat akurasi verifikasi $> 98\%$.
   * **Good Performer** ($75 \le \text{Score} < 90$): SLA $12 - 24$ jam, penanganan stabil.
   * **Needs Attention** ($\text{Score} < 75$): SLA $> 24$ jam atau tingkat penolakan berkas tidak wajar.
4. **Otomasi Pencatatan Sesi Kemenkes (*Zero-Manual Session Logging*)**:
   * Saat Admin POGI atau Super Admin menekan tombol **"Akses Portal Kemenkes"** di dashboard, header navigasi, atau rincian portofolio dokter, sistem secara otomatis mengarahkan ke portal resmi **SatuSehat SDMK Kemenkes RI (`https://satusehat.kemkes.go.id/sdmk/login`)** melalui rute `GET /kemenkes/launch` atau `POST /kemenkes/track-access`.
   * Pada saat yang sama, sistem secara otomatis merekam log riwayat kerja ke tabel `verifier_performance_logs` (`action_type = 'KEMENKES_SYNC_SESSION'`) dan `audit_logs` (`action = 'KEMENKES_PORTAL_ACCESS'`).
   * Mekanisme ini meniadakan kebutuhan input manual jam kerja verifikator, sehingga metrik produktivitas dan KPI admin terukur secara transparan dan akurat.

---

## 7. Matriks Hak Akses 5 Peran Resmi (RBAC Matrix)

Mengakomodasi penyesuaian peran operasional klien (Bu Dian, Bp. Ade, Bp. Ircham, Anna, Vivi, Lily, dr. Ulul, Bu Ratna, dan Cabang Daerah):

| Modul & Tindakan | Super Admin (Bu Dian, Ade, Ircham) | POGI Pusat (dr. Ulul, Bu Ratna) | Admin POGI (Anna, Vivi, Lily) | Admin Cabang (Daerah) | User / Dokter |
|:---|:---:|:---:|:---:|:---:|:---:|
| **Dashboard Nasional (38 Provinsi)** | Full Access | **Read Only (Cek Saja)** | Read Only | **Read Only (Cek Saja Cabangnya)** | ❌ Ditolak |
| **Direktori Dokter Seluruh Indonesia** | Full Access | **Read Only (Cek Saja 38 Wilayah)** | Read & Update (Data Entry) | ❌ Ditolak (Terisolasi Cabang) | ❌ Ditolak |
| **Direktori Dokter Cabang Sendiri** | Full Access | **Read Only (Cek Saja)** | Read & Update | **Read Only (Cek Saja)** | ❌ Ditolak |
| **Portofolio Mandiri Saya Sendiri** | Full Access | Full Access | Full Access | Full Access | **Akses Mandiri** |
| **Penginputan & Verifikasi Berkas SKP** | Ya | ❌ Ditolak (Hanya Cek Saja) | **Ya (Update & Input Data)** | ❌ Ditolak (Hanya Cek Saja) | ❌ Ditolak |
| **Alihkan PIC Pengelola Dokter (`assigned_pic_id`)** | **Ya (Eksklusif Super Admin)** | ❌ Ditolak | ❌ Ditolak | ❌ Ditolak | ❌ Ditolak |
| **Redistribusi Beban Kerja (*Workload Balancing*)** | **Ya (Eksklusif Super Admin)** | ❌ Ditolak | ❌ Ditolak | ❌ Ditolak | ❌ Ditolak |
| **Akses Portal Kemenkes & Auto-Log Sesi** | Ya | Ya (Monitoring) | **Ya (Sesi Tercatat Otomatis)** | Ya (Monitoring) | ❌ Ditolak |
| **Kirim WA Blast Massal** | Ya | ❌ Ditolak | Ya (Binaan) | ❌ Ditolak | ❌ Ditolak |
| **Modul Pengaturan Master (`manage`)** | **Full Access** | ❌ Ditolak | ❌ Ditolak | ❌ Ditolak | ❌ Ditolak |
| **Monitoring Kinerja Admin (`admin-performance`)** | **Full Access (Rebalancer Aktif)** | Read Only (Cek Saja) | Read Only (SLA Pribadi) | ❌ Ditolak | ❌ Ditolak |
| **Export Monthly Report PDF / Invoice** | Ya | Ya (Audit / Arsip) | ❌ Ditolak | ❌ Ditolak | ❌ Ditolak |

---

## 8. Standar Dokumen Bukti SKP & Kebijakan Anti-Storage Bloat (Document Vault)

1. **Mandatori Tautan / URL Google Drive**:
   * Setiap berkas bukti pendukung (sertifikat kegiatan, logbook operasi/tindakan faskes, surat tugas pengabdian) **WAJIB** menyertakan URL Google Drive yang valid (`google_drive_url NOT NULL`).
   * Tautan harus dipastikan memiliki akses visibilitas minimal *"Anyone with the link can view"* atau di-share ke grup akun verifikator resmi POGI agar tim verifikator dapat meninjau keabsahan berkas tanpa hambatan otorisasi.
2. **Kebijakan Pengunggahan File Biner Langsung Bersifat Opsional**:
   * Pengunggahan file fisik biner (PDF/JPG/PNG) langsung ke storage internal sistem berstatus **OPSIONAL**.
   * Tujuan kebijakan ini adalah mitigasi risiko pembengkakan kapasitas disk (*storage bloat*) pada server aplikasi, menghemat biaya operasional cloud/VPS, dan mempercepat proses backup database serta restore sistem.
3. **Validasi & Proteksi Jika File Biner Diunggah**:
   * Jika opsi upload fisik digunakan oleh dokter/admin:
     - Ukuran maksimal dibatasi **10 MB per dokumen**.
     - Tipe konten dibatasi pada `application/pdf`, `image/jpeg`, dan `image/png`.
     - File biner wajib dihitung nilai hash SHA-256 (`sha256_checksum`) untuk mencegah duplikasi file identik dan menjamin integritas bukti medikolegal.
     - Akses berkas fisik wajib melalui *Presigned URL* yang kadaluarsa dalam 15 menit, tidak boleh diekspos melalui direktori publik server.
4. **Alur Verifikasi Bukti Fisik**:
   * Verifikator (Admin POGI / Admin Cabang) memvalidasi keabsahan data dengan membuka tautan Google Drive tersebut, mencocokkan nomor sertifikat/jam pelatihan dengan SKP yang diklaim, lalu menetapkan status `DONE` (diterima) atau `REJECTED` (ditolak disertai alasan spesifik).

---

## 9. Kepatuhan Hukum Perlindungan Data Pribadi (UU PDP No. 27/2022)

1. **Enkripsi Data Sensitif Medis (*Encryption at Rest*)**:
   * Data identitas kependudukan (NIK) dan kredensial Plataran Sehat dokter wajib dienkripsi pada database menggunakan algoritma **AES-256-GCM** (`nik_encrypted BYTEA`).
   * Kunci enkripsi dikelola secara terpisah melalui konfigurasi lingkungan (`APP_KEY` / `ENCRYPTION_MASTER_KEY`).
2. **Deterministic Lookup Tanpa Dekripsi (*Zero Decrypt Lookup*)**:
   * Pencarian dokter berdasarkan NIK dilakukan menggunakan kolom hash deterministik **SHA-256 HMAC** (`nik_hash`). Database tidak perlu mendekripsi seluruh baris data hanya untuk mencari satu NIK (*No fullscan decryption*).
3. **Pencatatan Audit Trail Permanen (*Immutable Audit Logs*)**:
   * Setiap aktivitas melihat data sensitif, mengubah data, memverifikasi/menolak SKP, mengirim pesan WA, dan mengunduh laporan PDF wajib dicatat dalam tabel `audit_logs` bersama identitas pengguna, IP Address, dan User Agent.

---

## 10. Standar Engineering & Optimasi Database

1. **Larangan Keras ID Auto-Increment**:
   * Seluruh tabel dilarang menggunakan integer auto-increment untuk mencegah kebocoran data terenumerasi (*IDOR attack*).
   * Seluruh tabel wajib menggunakan **RFC 9562 UUIDv7** berbasis waktu untuk menjamin performa indeks pohon B-Tree PostgreSQL tetap sekuensial dan terurut.
2. **Jaminan Bebas Full Table Scan (*Zero Full-Scan Guarantee*)**:
   * Semua query wajib melewati indeks:
     - Covering Index `idx_skp_entries_doc_period_domain` dengan klausa `INCLUDE (credit_amount)` untuk agregasi instan.
     - Composite Partial Indexes `idx_doctors_branch_status` dan `idx_doctors_pic_status` untuk isolasi query.
     - Indeks GIN Trigram `idx_doctors_trgm_search` untuk pencarian teks nama dan nomor anggota.
3. **Pencegahan Masalah N+1**:
   * Pada lapisan ORM/Repository, pemanggilan relasi wajib menggunakan *Eager Loading* eksplisit (`with(['branch', 'province', 'activePeriod'])`).
4. **Paginasi Efisien**:
   * Query volume besar wajib menggunakan *Cursor Pagination* (`cursorPaginate()`) dan *Chunking* (`chunkById()`).

---

## 11. Arsitektur Server-Side Rendering (SSR) & Eliminasi Data Tiruan (Mock Fallback)

1. **Prinsip Eliminasi Mutlak Data Tiruan (*Zero-Mock Guarantee*)**:
   * Sesuai mandat arsitektur produksi, **seluruh halaman aplikasi tanpa terkecuali** (`/dashboard`, `/doctors`, `/doctor-detail/{id}`, `/provinces`, `/events`, `/reports`, `/admin-performance`, `/activity`, `/manage`) **dilarang keras menggunakan data statis hardcode atau fallback ke mock store client-side (`PogiStore`)**.
   * Seluruh kartu metrik, angka agregasi, tabel, opsi filter wilayah/faskes, dan grafik analitik dihitung dan di-query secara langsung dari basis data relasional MySQL/PostgreSQL melalui dedicated controller:
     - `DashboardController`: Ringkasan KPI nasional, rasio kepatuhan, sebaran provinsi, dan audit aktivitas terkini.
     - `DoctorController`: Direktori 200 dokter dengan relasi lengkap, isolasi cabang otomatis `ADMIN_CABANG`, portofolio 5 tahun, repositori berkas vault, dan detektor kekurangan dokumen.
     - `ProvinceController`: Capaian 38 provinsi terakreditasi, perangkingan kepatuhan, dan ringkasan nasional.
     - `EventController`: Katalog acara ilmiah SKP dan kategori ranah kompetensi.
     - `ActivityController`: Jejak audit trail sekuriti permanen dan metrik aktivitas harian.
     - `ReportController`: Snapshot cutoff bulanan resmi dan deret 12 bulan realisasi SKP riil.
     - `AdminPerformanceController`: Pengukuran SLA turnaround, volume binaan riil, akurasi, dan log kinerja verifikator.
     - `ManageController`: Master entitas lengkap (dokter, faskes/RS jejaring, kegiatan SKP, kategori, dan pengguna sistem).
2. **Pola Dual-Hydration SSR ke Client-Side Interactivity**:
   * Data dari basis data diinjeksi pada sisi server melalui Blade template dan disalurkan ke variabel global window berstatus immutable (`window.LIVE_*` / `window.__INITIAL_*__`).
   * Interaksi client-side seperti pencarian instan, filter multi-kategori, dan paginasi client beroperasi langsung di atas dataset riil yang telah dihidrasi dari server tanpa pernah menyentuh dummy data mock.
