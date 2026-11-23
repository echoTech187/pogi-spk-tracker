# RULES.md — Aturan Bisnis, Kepatuhan Regulasi & Standar Engineering
**Sistem Monitoring & Manajemen SKP Nasional POGI (PT Energi Cipta)**

---

## 1. Model Bisnis & Ruang Lingkup Operasional (B2B)
1. **Model Layanan B2B Eksklusif**: Sistem ini beroperasi murni sebagai platform **B2B (*Business-to-Business*)** antara **PT Energi Cipta** (pengelola operasional & teknologi) dan **POGI** (Perkumpulan Obstetri dan Ginekologi Indonesia) beserta pengurus cabang di seluruh Indonesia. Sistem tidak melayani pendaftaran ritel langsung dari dokter umum di luar keanggotaan POGI (*non-B2C*).
2. **Batasan Integrasi Kemenkes (Non-API / Manual-Assisted Sync)**:
   * Portal resmi SKP Kemenkes / Plataran Sehat **tidak menyediakan API publik** untuk penarikan maupun pengunggahan data secara otomatis.
   * Seluruh berkas bukti SKP yang terkumpul dari anggota atau pengurus cabang **wajib disimpan terlebih dahulu** di repositori penyimpanan cloud internal sistem (*Object Storage* / folder terstruktur per anggota) sebelum diunggah secara manual ke portal Kemenkes oleh tim Admin Energi Cipta.
   * Sistem **wajib** memiliki mekanisme pelacakan kekurangan dokumen (*missing document detector*) dan pencatatan *timestamp* terpisah antara pembaruan di dashboard internal.

---

## 2. Regulasi & Standar Perhitungan SKP (KMK HK.01.07/MENKES/1561/2024 & KMK HK.01.07/MENKES/1111/2026)
1. **Landasan Hukum & Standar Resmi**:
   * Penilaian dan pemenuhan SKP tenaga medis spesialis obstetri dan ginekologi mengacu pada Keputusan Menteri Kesehatan RI Nomor **KMK HK.01.07/MENKES/1561/2024** tentang Petunjuk Teknis Pengelolaan Satuan Kredit Profesi Tenaga Medis dan Tenaga Kesehatan serta pedoman penyempurna/penggantinya **KMK HK.01.07/MENKES/1111/2026**.
2. **Target Wajib Siklus 5 Tahun**:
   * Setiap Dokter Spesialis Obstetri dan Ginekologi (Sp.OG) wajib memenuhi target minimal **$160\text{ SKP}$** dalam periode masa berlaku SIP/STR selama **5 tahun** (setara target pemantauan rata-rata **$32\text{ SKP/tahun}$** atau **$8\text{ SKP/kuartal}$**).
3. **Proporsi Wajib Ranah Kompetensi & Fleksibilitas**:
   * **Ranah Pembelajaran (Minimal $45\%$)**: Minimal **$72\text{ SKP}$** dalam 5 tahun (seminar, workshop, simposium, pelatihan terakreditasi Plataran Sehat).
   * **Ranah Pelayanan (Minimal $35\%$)**: Minimal **$56\text{ SKP}$** dalam 5 tahun (praktik klinis, tindakan medis/operasi obstetri-ginekologi, pelayanan rumah sakit/faskes).
   * **Ranah Pengabdian Masyarakat & Profesi (Minimal $5\%$)**: Minimal **$8\text{ SKP}$** dalam 5 tahun (bakti sosial, penyuluhan kesehatan ibu & anak, penugasan daerah/bencana, kepengurusan organisasi).
   * **Sisa Fleksibilitas ($15\%$ atau $24\text{ SKP}$)**: Bebas dipenuhi dari kombinasi ketiga ranah di atas sesuai dengan aktivitas profesional masing-masing dokter untuk menggenapkan total minimal 160 SKP.
4. **Status Pencatatan Kredit SKP**:
   * Setiap entri kegiatan SKP diklasifikasikan ke dalam 4 status siklus hidup:
     1. `NOT_SUBMITTED` — Dokumen belum lengkap atau belum diajukan ke Kemenkes.
     2. `SUBMITTED` — Dokumen telah diverifikasi admin internal dan sudah diinput/diunggah ke portal Kemenkes.
     3. `DONE` — SKP telah terverifikasi dan disetujui resmi pada Buku Log Kemenkes.
     4. `REJECTED` — Pengajuan ditolak oleh verifikator Kemenkes dan memerlukan perbaikan berkas.

---

## 3. Siklus Cutoff Bulanan & Otomasi WhatsApp Blast
1. **Jadwal Cutoff Rutin**:
   * *Cutoff* data bulanan ditetapkan secara tetap pada **setiap tanggal 1 pukul 00:00 WIB**.
   * Snapshot capaian SKP per tanggal 1 dikunci sebagai dasar pembuatan laporan bulanan (*Monthly Report*) dan lampiran penagihan (*invoice*) kepada pihak POGI.
2. **Aturan Notifikasi & WA Blast Otomatis**:
   * **Pengingat Rutin Menjelang Cutoff**: Sistem menjalankan tugas terjadwal (*cron job*) pada **H-7** dan **H-2** sebelum tanggal 1 setiap bulan untuk mengirimkan pesan WhatsApp otomatis kepada anggota dokter yang masih memiliki kekurangan target SKP atau dokumen yang berstatus `NOT_SUBMITTED` / `REJECTED`.
   * **WA Blast Manual oleh Admin**: Admin operasional dapat memicu pengiriman WA Blast massal yang difilter berdasarkan Cabang POGI, Provinsi, atau status kelengkapan dokumen setelah melakukan *follow-up* dengan admin cabang daerah.
   * **Rate Limiting & Anti-Ban**: Pengiriman WA Blast wajib menggunakan antrean (*message queue*) dengan jeda acak (*jitter*) antar-pesan untuk menjaga reputasi nomor WhatsApp Business resmi Energi Cipta.

---

## 4. Aturan Visualisasi Peta Sebaran Wilayah (38 Provinsi)
Dashboard menampilkan peta interaktif 38 provinsi Indonesia untuk memetakan tingkat kepatuhan anggota POGI per daerah dengan kode warna baku sesuai kesepakatan operasional:
* **Merah (`#DC2626`) — Sangat Kurang**: Persentase pemenuhan target SKP kumulatif wilayah **$< 50\%$**. Memerlukan eskalasi segera ke pengurus cabang POGI setempat.
* **Hijau (`#16A34A`) — Hampir Memenuhi**: Persentase pemenuhan target SKP kumulatif wilayah berada pada rentang **$50\% \le \text{Capaian} < 85\%$**.
* **Kuning (`#EAB308`) — Terpenuhi / Aman**: Persentase pemenuhan target SKP kumulatif wilayah **$\ge 85\%$** (target terpenuhi atau berada di atas ambang batas aman).

---

## 5. Aturan Pelaporan Bulanan & Audit Sinkronisasi
1. **Arsip Laporan Bulanan (*Monthly Report PDF*)**:
   * Setiap laporan bulanan yang dihasilkan pada saat *cutoff* tanggal 1 wajib disimpan secara permanen (*immutable archive*) pada penyimpanan cloud dan dapat diunduh kembali kapan saja melalui pemilih bulan & tahun (*Month-Year Picker*) untuk kebutuhan audit atau lampiran *invoice*.
   * Format laporan mencakup ringkasan jumlah dokter terpantau, perbandingan total SKP aktual terhadap target, rincian per ranah (Pengabdian, Pelayanan, Pembelajaran), rekap *Year-to-Date* (YTD), dan rincian kredit berdasarkan status.
2. **Dual-Timestamp Tracking (Transparansi Pembaruan)**:
   * Setiap profil dokter wajib menampilkan dua penanda waktu yang berbeda secara eksplisit:
     * `dashboard_updated_at`: Waktu terakhir admin memperbarui data/berkas di sistem internal Energi Cipta.
     * `kemenkes_updated_at`: Waktu terakhir admin mengunggah berkas dan menyinkronkan status dari portal SKP Kemenkes.

---

## 6. Keamanan Data & Standar Engineering
1. **Kepatuhan Perlindungan Data Pribadi (UU PDP No. 27 Tahun 2022)**:
   * Data sensitif dokter (NIK, Nomor STR, Nomor SIP, kredensial akses Plataran Sehat jika dititipkan untuk bantuan input, serta nomor WhatsApp) wajib dienkripsi pada level penyimpanan (*encryption at rest* menggunakan **AES-256-GCM**) dan komunikasi (*TLS 1.3*).
   * Seluruh akses dan perubahan data oleh admin dicatat dalam tabel `audit_logs` secara permanen.
2. **Standar Pengkodean & Database**:
   * Gunakan **UUIDv7** sebagai *Primary Key* seluruh tabel untuk efisiensi indeks B-Tree berbasis waktu.
   * Terapkan *Role-Based Access Control* (**RBAC**) ketat dengan pemisahan hak akses antara `SUPER_ADMIN` (Energi Cipta), `ADMIN_OPERASIONAL` (Tim Input & Verifikasi), `ADMIN_CABANG` (Pengurus POGI Daerah — *read/upload only* untuk cabangnya), dan `VIEWER_POGI` (Pengurus Pusat POGI untuk memantau laporan & *invoice*).
   * Setiap *endpoint* mutasi data wajib bersifat *idempotent* dan dilindungi validasi skema ketat (*Zod* / *Pydantic*).