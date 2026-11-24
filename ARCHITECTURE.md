# ARCHITECTURE.md — Cetak Biru Arsitektur Sistem, ERD, DFD & Alur Kerja
**Sistem Monitoring & Manajemen SKP Nasional POGI (MedCred Enterprise)**
*Dikelola oleh PT Energi Cipta untuk Perkumpulan Obstetri dan Ginekologi Indonesia (POGI)*

---

## 1. Ikhtisar Arsitektur Sistem (*High-Level Architecture*)

Sistem dibangun menggunakan pendekatan **Clean Architecture (Hexagonal Architecture) Monolith dengan Asynchronous Worker Pipeline** untuk menjamin skalabilitas, kemudahan pengujian unit (*100% testable*), dan pemisahan aturan bisnis murni dari detail framework dan database.

```mermaid
flowchart TD
    subgraph ClientLayer["1. PRESENTATION LAYER (CLIENT & DELIVERY)"]
        WebAdmin["Web Console Admin (Laravel Blade + Tailwind CSS + Alpine.js)"]
        PortalDokter["Portal Dokter Mandiri (Mobile-Responsive Web)"]
        RoleSwitcher["Active Role Context (5 Official RBAC Roles)"]
    end

    subgraph AppLayer["2. APPLICATION LAYER (USE CASES)"]
        MW["Auth & RBAC Middleware"]
        Scope["BranchScope (Isolasi Cabang Otomatis)"]
        Controllers["Slim Controllers (Maksimal 3-5 Baris/Method)"]
        UseCases["Use Cases: VerifySkp, RecalculateCredits, CutoffJob, WaBlastJob"]
        DTOs["Readonly Data Transfer Objects (PHP 8.2 Strict Types)"]
    end

    subgraph DomainLayer["3. DOMAIN LAYER (CORE BUSINESS RULES)"]
        Entities["Domain Entities: Doctor, SkpPeriod, SkpEntry, Document"]
        ValueObjects["Value Objects: Nik, SkpCredit, PhoneNumber"]
        Contracts["Repository Interfaces & Service Contracts"]
        Events["Domain Events: SkpVerified, CutoffLocked"]
    end

    subgraph InfraLayer["4. INFRASTRUCTURE & PERSISTENCE LAYER"]
        Postgres[("PostgreSQL 14+ (RFC 9562 UUIDv7 + Zero-Fullscan Index)")]
        RedisQueue[("Redis Queue / Worker Daemon (BullMQ / Laravel Queue)")]
        DocVault[("Document Vault (Google Drive Mandatori + Opsional S3/MinIO)")]
        WAGateway["WhatsApp Business Gateway API (Anti-Ban Jitter)"]
    end

    ClientLayer --> MW
    MW --> Scope
    Scope --> Controllers
    Controllers --> UseCases
    UseCases --> DTOs
    UseCases --> Contracts
    UseCases --> Entities
    UseCases --> ValueObjects
    UseCases --> Events
    Contracts -.->|implemented by| InfraLayer
    UseCases --> RedisQueue
    RedisQueue --> WAGateway
    UseCases --> Postgres
    UseCases --> DocVault
```

---

## 2. Entity-Relationship Diagram (ERD)

Database dinormalisasi ke tingkat **3NF/BCNF** dengan seluruh tabel menggunakan **RFC 9562 UUIDv7** sebagai *Primary Key* dan terindeks penuh untuk menjamin **Zero Full Table Scan**.

```mermaid
erDiagram
    provinces ||--o{ pogi_branches : "has"
    provinces ||--o{ institutions : "located in"
    provinces ||--o{ doctors : "governs"
    pogi_branches ||--o{ users : "assigns"
    pogi_branches ||--o{ doctors : "manages"
    institutions ||--o{ doctors : "primary hospital"
    institutions ||--o{ doctor_additional_institutions : "network hospital"

    users ||--o{ user_refresh_tokens : "owns"
    users ||--o{ doctors : "assigned as PIC"
    users ||--o{ doctors : "linked account"
    users ||--o{ audit_logs : "records"
    users ||--o{ verifier_performance_logs : "logs SLA"

    doctors ||--o{ doctor_additional_institutions : "practices at"
    doctors ||--o{ doctor_skp_periods : "evaluates 5-year cycle"
    doctors ||--o{ skp_entries : "submits"
    doctors ||--o{ doctor_documents : "stores in vault"
    doctors ||--o{ monthly_doctor_cutoff_details : "snapshot item"
    doctors ||--o{ wa_message_queue : "receives reminder"

    event_categories ||--o{ skp_events : "categorizes"
    event_categories ||--o{ skp_entries : "domain rule"
    skp_events ||--o{ skp_entries : "references event"

    doctor_skp_periods ||--o{ skp_entries : "aggregates"
    skp_entries ||--o{ doctor_documents : "verified by"
    skp_entries ||--o{ verifier_performance_logs : "audits SLA"

    monthly_cutoff_snapshots ||--o{ monthly_doctor_cutoff_details : "contains details"
    wa_blast_campaigns ||--o{ wa_message_queue : "queues messages"

    provinces {
        varchar(10) id PK "Kode BPS (e.g. 31, 32)"
        varchar(100) name
        varchar(50) region_island
        decimal latitude
        decimal longitude
    }

    pogi_branches {
        uuid id PK "RFC 9562 UUIDv7"
        varchar(10) province_id FK
        varchar(50) branch_code "POGI-32"
        varchar(150) branch_name
        text secretariat_address
        varchar(100) contact_email
        varchar(30) contact_phone
    }

    institutions {
        uuid id PK "RFC 9562 UUIDv7"
        varchar(10) province_id FK
        uuid branch_id FK
        varchar(50) code
        varchar(200) name
        varchar(50) institution_type
        varchar(10) hospital_class
    }

    users {
        uuid id PK "RFC 9562 UUIDv7"
        enum role "5 Official Roles"
        varchar(150) full_name
        varchar(150) email UK
        varchar(30) phone_number
        varchar(255) password_hash "Argon2id"
        uuid branch_id FK "Isolasi Cabang"
        boolean is_active
    }

    doctors {
        uuid id PK "RFC 9562 UUIDv7"
        uuid user_id FK "Tautan Login Mandiri"
        varchar(50) npa_pogi UK "Nomor Pokok Anggota"
        varchar(200) full_name
        varchar(100) specialization_title "Sp.OG"
        bytea nik_encrypted "AES-256-GCM (UU PDP)"
        varchar(64) nik_hash UK "HMAC-SHA256"
        varchar(50) str_number UK
        varchar(50) sip_number
        date sip_expiry_date
        varchar(150) email
        varchar(30) whatsapp_number
        uuid branch_id FK
        varchar(10) province_id FK
        uuid primary_institution_id FK
        uuid assigned_pic_id FK "Admin POGI PIC"
        enum status "COMPLETE | INCOMPLETE"
        timestamptz dashboard_updated_at
        timestamptz kemenkes_updated_at
    }

    doctor_skp_periods {
        uuid id PK "RFC 9562 UUIDv7"
        uuid doctor_id FK
        int cycle_number "1"
        date start_date
        date end_date "SIP Expiry"
        numeric target_total_credits "160.00"
        numeric target_pembelajaran_credits "72.00"
        numeric target_pelayanan_credits "56.00"
        numeric target_pengabdian_credits "8.00"
        numeric target_flexibility_credits "24.00"
        numeric achieved_total_credits
        boolean is_active
        boolean is_completed
    }

    skp_entries {
        uuid id PK "RFC 9562 UUIDv7"
        uuid doctor_id FK
        uuid period_id FK
        uuid event_id FK
        uuid category_id FK
        enum domain "PEMBELAJARAN | PELAYANAN | PENGABDIAN"
        varchar(255) title
        date activity_date
        numeric credit_amount
        enum status "DONE | SUBMITTED | REJECTED | NOT_SUBMITTED"
        uuid verified_by_user_id FK
        timestamptz verified_at
        text rejection_reason
    }

    doctor_documents {
        uuid id PK "RFC 9562 UUIDv7"
        uuid doctor_id FK
        uuid skp_entry_id FK
        enum document_type
        varchar(255) file_name
        text google_drive_url "MANDATORI (Mencegah Storage Penuh)"
        bigint file_size_bytes "Opsional (Max 10MB)"
        varchar(100) mime_type "Opsional"
        varchar(100) storage_bucket "google-drive / s3"
        varchar(500) storage_object_key "Opsional"
        char(64) sha256_checksum "Opsional"
        boolean is_verified
        uuid verified_by_user_id FK
        timestamptz verified_at
        uuid uploaded_by_user_id FK
    }

    monthly_cutoff_snapshots {
        uuid id PK "RFC 9562 UUIDv7"
        int period_year
        int period_month
        timestamptz cutoff_timestamp "Tgl 1, 00:00 WIB"
        int total_doctors_monitored "200"
        int total_complete_doctors
        int total_incomplete_doctors
        varchar(255) pdf_storage_key "Invoice Attachment"
        boolean is_locked "TRUE"
    }

    wa_blast_campaigns {
        uuid id PK "RFC 9562 UUIDv7"
        varchar(200) title
        varchar(50) trigger_type "H-7 | H-2 | MANUAL"
        text message_template
        int total_recipients
    }

    wa_message_queue {
        uuid id PK "RFC 9562 UUIDv7"
        uuid campaign_id FK
        uuid doctor_id FK
        varchar(30) recipient_phone
        text message_body
        enum status "QUEUED | SENDING | SENT | FAILED"
        int jitter_ms "1500 - 3000ms"
        timestamptz scheduled_at
        timestamptz sent_at
    }

    audit_logs {
        uuid id PK "RFC 9562 UUIDv7"
        uuid user_id FK
        enum action "LOGIN | VERIFY_SKP | VIEW_SENSITIVE"
        varchar(100) entity_name
        text new_values
        inet ip_address
        timestamptz created_at
    }

    verifier_performance_logs {
        uuid id PK "RFC 9562 UUIDv7"
        uuid verifier_user_id FK "Admin POGI"
        uuid doctor_id FK
        uuid skp_entry_id FK
        int turnaround_time_seconds "SLA"
    }
```

---

## 3. Data Flow Diagram (DFD)

### 3.1 DFD Level 0 (Context Diagram)

Menampilkan batasan sistem dan entitas eksternal yang berinteraksi langsung:

```mermaid
flowchart TD
    DOKTER["Dokter Spesialis Obgyn (User/Dokter)"]
    CABANG["Admin Cabang POGI (Daerah)"]
    VERIF["Admin POGI (Verifikator Nasional)"]
    PUSAT["Admin Pusat & Pengurus PP POGI"]
    SUPER["Super Admin (PT Energi Cipta)"]
    WAGW["WhatsApp Business Gateway API"]
    KEMENKES["Portal Plataran Sehat Kemenkes (Manual Sync)"]

    SYS(("SISTEM MONITORING & MANAJEMEN SKP NASIONAL POGI (MedCred)"))

    DOKTER <-->|Login Mandiri, Input Tautan GDrive & Bukti, Cek Portofolio| SYS
    CABANG <-->|Verifikasi Berkas Cabangnya, Monitoring Wilayah| SYS
    VERIF <-->|Validasi Dokumen Binaan, Rekalkulasi Ranah, Alasan Tolak| SYS
    PUSAT <-->|Pantau Peta 38 Prov, Monitor SLA Verifikator, Unduh Invoice PDF| SYS
    SUPER <-->|Konfigurasi Master Data, User & Hak Akses, Audit Trail| SYS
    SYS -->|Pesan Pengingat H-7 & H-2 dengan Anti-Ban Jitter| WAGW
    SYS <-->|Sinkronisasi Status & Last Update Kemenkes| KEMENKES
```

---

### 3.2 DFD Level 1 (Dekomposisi Proses Inti)

```mermaid
flowchart TD
    subgraph P1["1.0 Manajemen Identitas & Hak Akses (RBAC)"]
        D1[("Users & Roles")]
    end

    subgraph P2["2.0 Repositori Dokumen & Verifikasi SKP"]
        D2[("Doctor Documents Vault (Google Drive URLs)")]
        D3[("SKP Entries Logbook")]
        D4[("Doctor SKP Periods")]
    end

    subgraph P3["3.0 Pemantauan Wilayah & Kinerja SLA"]
        D5[("Provinces & Branches")]
        D6[("Verifier SLA Logs")]
        D7[("Materialized View Peta")]
    end

    subgraph P4["4.0 Otomasi Cutoff Bulanan & Arsip PDF"]
        D8[("Monthly Cutoff Snapshots")]
        D9[("S3 PDF Storage")]
    end

    subgraph P5["5.0 Pengiriman Notifikasi & WA Blast"]
        D10[("WA Message Queue")]
    end

    subgraph P6["6.0 Audit Trail Kepatuhan UU PDP"]
        D11[("Audit Logs")]
    end

    P1 --> D1
    P2 --> D2
    P2 --> D3
    P2 --> D4
    P2 --> P3
    P3 --> D5
    P3 --> D6
    P3 --> D7
    P4 --> D8
    P4 --> D9
    P5 --> D10
    P2 --> P6
    P1 --> P6
    P6 --> D11
```

---

## 4. Alur Kerja Operasional (*Workflows*)

### 4.1 Workflow 1: Pengajuan, Verifikasi & Rekalkulasi Otomatis SKP

```mermaid
sequenceDiagram
    autonumber
    actor Dokter as Dokter / Admin Cabang
    participant GDrive as Google Drive (Storage Dokter)
    actor Verifikator as Admin POGI
    participant API as Backend (Laravel UseCase)
    participant DB as PostgreSQL 14+
    participant Audit as Audit Trail Service

    Dokter->>GDrive: Unggah berkas ke Google Drive dokter/cabang & atur share link
    Dokter->>API: Submit form SKP + Link Google Drive (Mandatori) & Opsional file biner
    API->>DB: INSERT skp_entries & doctor_documents (google_drive_url)
    
    Verifikator->>API: Buka halaman verifikasi dokter binaan
    API->>DB: Query skp_entries via Covering Index
    Verifikator->>GDrive: Buka & periksa keabsahan berkas via tautan Google Drive
    Verifikator->>API: Validasi berkas (Approve / Reject)
    
    alt Keputusan: APPROVE
        API->>DB: UPDATE skp_entries SET status = 'DONE'
        DB-->>DB: Trigger 'trigger_recalculate_doctor_skp' aktif otomatis
        DB-->>DB: Hitung Pembelajaran, Pelayanan, Pengabdian, Fleksibilitas
        DB-->>DB: Update status dokter (COMPLETE jika >= 160)
        API->>DB: INSERT verifier_performance_logs (catat SLA waktu)
    else Keputusan: REJECT
        API->>DB: UPDATE skp_entries SET status = 'REJECTED', rejection_reason = '...'
    end

    API->>Audit: Catat aksi ke audit_logs (User ID, IP, Action)
    API-->>Verifikator: Respon sukses & pembaruan kartu portofolio
```

---

### 4.2 Workflow 2: Cutoff Bulanan Tanggal 1 & Pembuatan Laporan Resmi

```mermaid
sequenceDiagram
    autonumber
    participant Cron as Laravel Task Scheduler
    participant Worker as Monthly Cutoff Worker
    participant DB as PostgreSQL 14+
    participant PDF as PDF Renderer (WeasyPrint/Puppeteer)
    participant Storage as Object Storage (S3)
    actor Pusat as Pengurus Pusat POGI

    Note over Cron: Tepat Tanggal 1 pukul 00:00:00 WIB
    Cron->>Worker: Trigger GenerateMonthlyCutoffJob
    Worker->>DB: Eksekusi snapshot query 200 dokter
    DB-->>Worker: Data terkunci (total SKP, complete, incomplete)
    Worker->>DB: INSERT monthly_cutoff_snapshots (is_locked = TRUE)
    Worker->>DB: INSERT 200 rows monthly_doctor_cutoff_details
    
    Worker->>PDF: Render Laporan Resmi Cutoff (Monthly Report)
    PDF-->>Worker: File PDF Laporan Resmi
    Worker->>Storage: Simpan PDF (pogi-medcred-vault/reports/...)
    Worker->>DB: UPDATE pdf_storage_key & pdf_sha256 checksum
    
    Pusat->>Worker: Akses Menu Laporan via Month-Year Picker
    Worker->>Storage: Generate Presigned URL download PDF
    Storage-->>Pusat: Unduh Dokumen Resmi Lampiran Invoice Penagihan
```

---

### 4.3 Workflow 3: Antrean Notifikasi WA Blast dengan Anti-Ban Jitter

```mermaid
sequenceDiagram
    autonumber
    participant Trigger as Cron H-7 / H-2 / Manual Admin
    participant Queue as WA Queue Dispatcher UseCase
    participant DB as MySQL 8+ / PostgreSQL 14+
    participant Worker as Background Queue Worker
    participant WAGW as WhatsApp Business Gateway API
    actor Dokter as Dokter Sp.OG

    Trigger->>Queue: Filter dokter target (Status Incomplete / Not Submitted)
    Queue->>DB: Query dokter via Index Scan 'idx_doctors_branch_status'
    Queue->>DB: Batch INSERT wa_message_queue (status: 'QUEUED', jitter: 1500-3000ms)
    
    loop Setiap Pesan dalam Antrean (Rate Limit: 15 pesan/menit)
        Worker->>DB: Ambil 1 pesan status 'QUEUED'
        Worker->>Worker: Sleep acak sesuai jitter (misal 2.140 ms)
        Worker->>WAGW: POST /v1/send-message (Recipient & Body)
        alt Pengiriman Berhasil
            WAGW-->>Worker: 200 OK (Message ID)
            Worker->>DB: UPDATE status = 'SENT', sent_at = NOW()
            WAGW->>Dokter: Kirim pesan WhatsApp pengingat cutoff
        else Pengiriman Gagal
            WAGW-->>Worker: Error / Timeout
            Worker->>DB: UPDATE status = 'FAILED', failure_reason = '...', retry_count++
        end
    end
```

---

### 4.4 Workflow 4: Sesi Akses Portal Kemenkes & Auto-Log Kinerja Admin (Zero-Manual Logging)

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin POGI (Anna / Vivi / Lily)
    participant UI as Dashboard / Doctor Detail View
    participant Ctrl as KemenkesController (trackAccess)
    participant DB as Basis Data (MySQL 8+)
    participant Portal as Portal Plataran Sehat (Kemenkes)

    Admin->>UI: Klik Tombol "Akses Portal Kemenkes"
    UI->>Ctrl: POST /kemenkes/track-access (doctor_id, doctor_name)
    Ctrl->>DB: INSERT verifier_performance_logs (action_type='KEMENKES_SYNC_SESSION')
    Ctrl->>DB: INSERT audit_logs (action='KEMENKES_PORTAL_ACCESS')
    opt Jika ada konteks dokter
        Ctrl->>DB: UPDATE doctors SET kemenkes_updated_at = NOW()
    end
    Ctrl-->>UI: 200 OK (portal_url: https://satusehat.kemkes.go.id, status: 'success')
    UI->>UI: Tampilkan Toast "Sesi kerja Kemenkes tercatat otomatis!"
    UI->>Portal: Buka tab baru portal Plataran Sehat Kemenkes
    Note over Admin,Portal: Admin bekerja tanpa perlu mencatat manual jam mulai/selesai kerja
```

---

### 4.5 Workflow 5: Penetapan & Pengalihan Admin PIC Dokter oleh Super Admin

```mermaid
sequenceDiagram
    autonumber
    actor Super as Super Admin (Bu Dian / Bp. Ade / Bp. Ircham)
    actor NonSuper as Non-Super Admin (Admin POGI / Pusat / Cabang)
    participant UI as Doctor Detail / Rebalancer Modal
    participant Ctrl as DoctorController (assignPic)
    participant DB as Basis Data (MySQL 8+)

    NonSuper->>Ctrl: POST /doctors/{id}/assign-pic
    Ctrl-->>NonSuper: 403 Forbidden (Otoritas Eksklusif Super Admin)

    Super->>UI: Pilih Dokter & Tentukan PIC Baru (Admin POGI)
    UI->>Ctrl: POST /doctors/{id}/assign-pic (assigned_pic_id)
    Ctrl->>Ctrl: Validasi Role Super Admin & UUID PIC
    Ctrl->>DB: UPDATE doctors SET assigned_pic_id = target_pic_id, updated_at = NOW()
    Ctrl->>DB: INSERT audit_logs (action='REASSIGN_DOCTOR_PIC', old_pic, new_pic)
    Ctrl-->>UI: 200 OK (status: 'success', message: 'PIC dokter berhasil dialihkan')
    UI->>UI: Update Tampilan Badge PIC Dokter & Tampilkan Toast
```

---

## 5. Batasan Sistem & Kendala Teknis (*System Boundaries & Constraints*)

1. **Batasan Integrasi Kemenkes Plataran Sehat & Otomasi Riwayat Sesi**:
   * Portal Kemenkes Plataran Sehat **tidak menyediakan Webhook atau REST API publik**.
   * Sistem beroperasi secara *Assisted-Sync*: berkas diinputkan secara manual ke portal Kemenkes oleh tim Admin POGI (Anna, Vivi, Lily).
   * Untuk meringankan beban administrasi, saat admin mengklik **"Akses Portal Kemenkes"**, sistem secara otomatis merekam log sesi kerja ke `verifier_performance_logs` dan `audit_logs` sehingga SLA dan metrik produktivitas terhitung secara *real-time* tanpa input manual.
2. **Batasan Wewenang Peran (*Role Boundaries*)**:
   * **Super Admin (Bu Dian, Bp. Ade, Bp. Ircham)**: Pemegang wewenang penuh, master data, pengalihan PIC dokter (`assigned_pic_id`), redistribusi beban (*workload rebalancing*), dan penguncian laporan bulanan.
   * **Admin POGI (Anna, Vivi, Lily)**: Pelaksana data entry dan verifikasi berkas SKP. Tidak memiliki akses mengubah PIC dokter.
   * **POGI Pusat (dr. Ulul, Bu Ratna)**: Pemantauan eksekutif nasional (**Hanya bisa cek saja**), dapat meninjau 38 provinsi tanpa batasan, namun tidak dapat mengedit data dokter.
   * **Admin Cabang (Pengurus Daerah)**: Pemantauan cabang daerah (**Hanya bisa cek saja**), terisolasi ketat hanya untuk wilayah cabangnya sendiri melalui *BranchScope*. Tidak dapat mengedit data dokter.
3. **Batasan Rate-Limiting WhatsApp Gateway**:
   * WhatsApp Business Gateway membatasi *throughput* pengiriman maksimal **15 pesan per menit** per nomor resmi.
   * Setiap pesan wajib diproses asinkron menggunakan antrean (*Queue Worker*) dengan delay acak (*jitter*) **1.500 ms – 3.000 ms** untuk mencegah blokir nomor oleh algoritma anti-spam Meta.
4. **Batasan Repositori Berkas (Document Vault) & Kebijakan Anti-Storage Bloat**:
   * **URL / Tautan Google Drive adalah MANDATORI**: Setiap pengajuan bukti fisik SKP (sertifikat simposium, logbook tindakan, surat tugas) **WAJIB** menyertakan URL tautan Google Drive yang dapat diakses (akses share *view*).
   * **Pencegahan Pembengkakan Storage Server (*Storage Bloat Prevention*)**: Kebijakan ini diberlakukan secara tegas karena pengurus dan *stakeholder* khawatir kapasitas *storage* server cepat penuh dan membengkak drastis jika menampung puluhan ribu berkas biner (PDF/gambar) dari ribuan dokter spesialis obgyn di seluruh Indonesia.
   * **Upload Berkas Biner Fisik Langsung Bersifat OPSIONAL**: Sistem tetap menyediakan opsi upload berkas biner langsung ke disk/Object Storage jika sewaktu-waktu dibutuhkan, namun bukan merupakan syarat wajib submit. Jika berkas diunggah, batas maksimal ukuran adalah **10 MB per dokumen** (format PDF, JPG, PNG) dan diamankan dengan *Presigned URL berbatas waktu (maksimal 15 menit)*.
5. **Batasan Isolasi Multi-Tenant Cabang**:
   * Admin Cabang dibatasi secara ketat hanya dapat melihat dokter yang terdaftar di wilayah cabangnya sendiri melalui penerapan **Eloquent Global Scope (`BranchScope`)**. Akses ke dokter cabang lain diblokir otomatis dengan kode HTTP `403 Forbidden`.

---

## 6. Standar Optimasi Database & Query (*Zero Full-Scan Architecture*)

Untuk menjamin latensi respon server tetap **< 50ms** saat data bertumbuh, arsitektur database menerapkan aturan pengindeksan presisi:

```mermaid
flowchart TD
    subgraph Indexes["Arsitektur Indeks Zero-Fullscan (PostgreSQL 14+)"]
        I1["Covering Index: idx_skp_entries_doc_period_domain (doctor_id, period_id, domain, status) INCLUDE (credit_amount)"]
        I2["GIN Trigram Index: idx_doctors_trgm_search ON doctors USING gin(full_name gin_trgm_ops, npa_pogi gin_trgm_ops, str_number gin_trgm_ops)"]
        I3["Composite Partial Index: idx_doctors_branch_status ON doctors(branch_id, status)"]
        I4["Composite Partial Index: idx_doctors_pic_status ON doctors(assigned_pic_id, status)"]
        I5["Partial Queue Index: idx_wa_queue_pending ON wa_message_queue(scheduled_at) WHERE status = 'QUEUED'"]
        I6["Materialized View: mv_province_compliance_summary (Peta 38 Provinsi Caching)"]
    end
```

* **Covering Index (`INCLUDE`)**: Query agregasi dan penghitungan pemenuhan ranah SKP menghasilkan **Index-Only Scan** (*zero heap read*) karena kolom `credit_amount` tersimpan langsung di dalam daun pohon B-Tree indeks.
* **Pencarian Cepat GIN Trigram**: Pencarian dokter berdasarkan nama, NPA POGI, atau nomor STR memanfaatkan ekstensi `pg_trgm` pada indeks GIN, membebaskan CPU dari *sequential table scan*.
* **Pencegahan N+1 Query**: Wajib memuat relasi relasional menggunakan *Eager Loading* (`with(['branch', 'province', 'activePeriod'])`).
* **Materialized View Caching**: Peta kepatuhan 38 provinsi di-cache pada `mv_province_compliance_summary` dan diperbarui secara berkala / terpicu transaksi.
