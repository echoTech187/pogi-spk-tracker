-- ============================================================================
-- POGI SKP TRACKER (MedCred) — PRODUCTION DATABASE SCHEMA
-- Perkumpulan Obstetri dan Ginekologi Indonesia (POGI) x PT Energi Cipta
-- Engine: PostgreSQL 16+ (ACID Compliant, 3NF / BCNF Normalized, Zero-Fullscan Indexing)
-- Standard: KMK HK.01.07/MENKES/1561/2024 & KMK HK.01.07/MENKES/1111/2026
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 0. EXTENSIONS & PREREQUISITES
-- ----------------------------------------------------------------------------
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pgcrypto";
CREATE EXTENSION IF NOT EXISTS "pg_trgm";
CREATE EXTENSION IF NOT EXISTS "btree_gist";

-- ----------------------------------------------------------------------------
-- 1. ENUMS (CUSTOM TYPES)
-- ----------------------------------------------------------------------------
DO $$ BEGIN
    CREATE TYPE system_role_enum AS ENUM (
        'SUPER_ADMIN',
        'ADMIN_PUSAT',
        'ADMIN_POGI',
        'ADMIN_CABANG',
        'DOKTER'
    );
EXCEPTION WHEN duplicate_object THEN null; END $$;

DO $$ BEGIN
    CREATE TYPE skp_domain_enum AS ENUM (
        'PEMBELAJARAN',      -- Target min 45% (72 SKP / 5 thn)
        'PELAYANAN',         -- Target min 35% (56 SKP / 5 thn)
        'PENGABDIAN',        -- Target min 5%  (8 SKP / 5 thn)
        'PUBLIKASI_ILMIAH'   -- Fleksibilitas 15% (24 SKP)
    );
EXCEPTION WHEN duplicate_object THEN null; END $$;

DO $$ BEGIN
    CREATE TYPE skp_entry_status_enum AS ENUM (
        'NOT_SUBMITTED',     -- Belum diunggah/diajukan ke Kemenkes
        'SUBMITTED',         -- Sudah diverifikasi admin & diinput ke Plataran Sehat
        'DONE',              -- Disetujui resmi di log Kemenkes
        'REJECTED'           -- Ditolak verifikator Kemenkes / perlu revisi
    );
EXCEPTION WHEN duplicate_object THEN null; END $$;

DO $$ BEGIN
    CREATE TYPE doctor_compliance_status_enum AS ENUM (
        'COMPLETE',          -- Memenuhi target kuartalan / siklus 160 SKP
        'INCOMPLETE',        -- Masih memiliki kekurangan kredit / dokumen
        'PENDING_REVIEW'     -- Ada berkas dalam antrean verifikasi
    );
EXCEPTION WHEN duplicate_object THEN null; END $$;

DO $$ BEGIN
    CREATE TYPE document_type_enum AS ENUM (
        'SERTIFIKAT_PEMBELAJARAN',
        'LOGBOOK_PELAYANAN',
        'SURAT_TUGAS_PENGABDIAN',
        'DOKUMEN_STR',
        'DOKUMEN_SIP',
        'IDENTITAS_KTP',
        'LAINNYA'
    );
EXCEPTION WHEN duplicate_object THEN null; END $$;

DO $$ BEGIN
    CREATE TYPE wa_queue_status_enum AS ENUM (
        'QUEUED',
        'PROCESSING',
        'SENT',
        'DELIVERED',
        'FAILED'
    );
EXCEPTION WHEN duplicate_object THEN null; END $$;

DO $$ BEGIN
    CREATE TYPE wa_trigger_type_enum AS ENUM (
        'CRON_H_MINUS_7',
        'CRON_H_MINUS_2',
        'MANUAL_ADMIN_BLAST',
        'INDIVIDUAL_REMINDER'
    );
EXCEPTION WHEN duplicate_object THEN null; END $$;

-- ----------------------------------------------------------------------------
-- 2. DOMAIN 1: MASTER GEOGRAFIS & FASILITAS KESEHATAN (3NF Master Data)
-- ----------------------------------------------------------------------------

-- 2.1 Master 38 Provinsi Indonesia (Kemendagri / BPS Standard)
CREATE TABLE IF NOT EXISTS provinces (
    id VARCHAR(10) PRIMARY KEY,                  -- Kode Wilayah BPS (misal '32' untuk Jawa Barat)
    name VARCHAR(100) NOT NULL UNIQUE,          -- Nama Resmi Provinsi
    region_island VARCHAR(50) NOT NULL,          -- Pulau (Jawa, Sumatera, dll)
    latitude DECIMAL(9,6),                       -- Koordinat Geografis Peta Interaktif
    longitude DECIMAL(9,6),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- 2.2 Master Cabang POGI Daerah
CREATE TABLE IF NOT EXISTS pogi_branches (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    province_id VARCHAR(10) NOT NULL REFERENCES provinces(id) ON DELETE RESTRICT,
    branch_code VARCHAR(30) NOT NULL UNIQUE,     -- e.g. 'POGI-JABAR', 'POGI-JATIM'
    branch_name VARCHAR(150) NOT NULL,           -- e.g. 'POGI Cabang Jawa Barat'
    secretariat_address TEXT,
    contact_email VARCHAR(150),
    contact_phone VARCHAR(30),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- 2.3 Master Rumah Sakit & Jejaring Faskes Praktik
CREATE TABLE IF NOT EXISTS institutions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    province_id VARCHAR(10) NOT NULL REFERENCES provinces(id) ON DELETE RESTRICT,
    branch_id UUID REFERENCES pogi_branches(id) ON DELETE SET NULL,
    code VARCHAR(50) UNIQUE,
    name VARCHAR(200) NOT NULL,
    institution_type VARCHAR(50) NOT NULL,       -- RSUP, RSUD, RS Swasta, Puskesmas, dll
    hospital_class VARCHAR(10),                  -- Kelas A, B, C, D
    address TEXT,
    city VARCHAR(100),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ----------------------------------------------------------------------------
-- 3. DOMAIN 2: IDENTITAS, AKUN PENGGUNA & RBAC 5 ROLE (Identity & Access Control)
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    role system_role_enum NOT NULL,              -- 5 Role Resmi Sistem
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone_number VARCHAR(30),
    password_hash VARCHAR(255) NOT NULL,         -- Argon2id Hash
    branch_id UUID REFERENCES pogi_branches(id) ON DELETE SET NULL, -- Wajib untuk ADMIN_CABANG
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    email_verified_at TIMESTAMPTZ,
    last_login_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ                       -- Soft Delete
);

-- Token Refresh Sesi Aktif (Multi-device JWT Security)
CREATE TABLE IF NOT EXISTS user_refresh_tokens (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    token_hash VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at TIMESTAMPTZ NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ----------------------------------------------------------------------------
-- 4. DOMAIN 3: MASTER DOKTER & SIKLUS SKP (Doctor Master Profile & 5-Year Cycles)
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS doctors (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID UNIQUE REFERENCES users(id) ON DELETE SET NULL, -- Tautan akun login mandiri
    npa_pogi VARCHAR(50) NOT NULL UNIQUE,        -- Nomor Pokok Anggota POGI (Kredensial Unik)
    full_name VARCHAR(200) NOT NULL,             -- Nama Lengkap Beserta Gelar Depan
    specialization_title VARCHAR(100) NOT NULL DEFAULT 'Sp.OG', -- Gelar Spesialis / Subspesialis
    
    -- UU PDP: Enkripsi Data Sensitif (AES-256-GCM) + HMAC Hash untuk Deterministic Lookup
    nik_encrypted BYTEA NOT NULL,
    nik_hash VARCHAR(64) NOT NULL UNIQUE,        -- SHA256 HMAC (Pencarian NIK tanpa fullscan / decrypt)
    str_number VARCHAR(50) NOT NULL UNIQUE,      -- Nomor STR Kemenkes
    sip_number VARCHAR(50),                      -- Nomor SIP Utama
    sip_expiry_date DATE NOT NULL,               -- Tanggal Kadaluarsa SIP (Dasar Cutoff Siklus)
    
    email VARCHAR(150),
    whatsapp_number VARCHAR(30) NOT NULL,        -- Target Pengingat Notifikasi WA
    branch_id UUID NOT NULL REFERENCES pogi_branches(id) ON DELETE RESTRICT,
    province_id VARCHAR(10) NOT NULL REFERENCES provinces(id) ON DELETE RESTRICT,
    primary_institution_id UUID REFERENCES institutions(id) ON DELETE SET NULL,
    assigned_pic_id UUID REFERENCES users(id) ON DELETE SET NULL, -- Admin POGI Pengampu/PJ
    
    -- Kredensial Titipan Plataran Sehat Kemenkes (Terenkripsi)
    plataran_sehat_username VARCHAR(100),
    plataran_sehat_pass_encrypted BYTEA,
    
    status doctor_compliance_status_enum NOT NULL DEFAULT 'INCOMPLETE',
    
    -- Dual-Timestamp Tracking (Transparansi Sinkronisasi)
    dashboard_updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    kemenkes_updated_at TIMESTAMPTZ,
    
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ
);

-- Relasi Tambahan Faskes Jejaring (Dokter Berpraktik di > 1 RS / Klinik)
CREATE TABLE IF NOT EXISTS doctor_additional_institutions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    doctor_id UUID NOT NULL REFERENCES doctors(id) ON DELETE CASCADE,
    institution_id UUID NOT NULL REFERENCES institutions(id) ON DELETE RESTRICT,
    sip_number VARCHAR(50),
    sip_expiry_date DATE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (doctor_id, institution_id)
);

-- Siklus Wajib 5 Tahun SKP (KMK 1561/2024 & KMK 1111/2026: 160 SKP / 5 Tahun)
CREATE TABLE IF NOT EXISTS doctor_skp_periods (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    doctor_id UUID NOT NULL REFERENCES doctors(id) ON DELETE CASCADE,
    cycle_number INT NOT NULL DEFAULT 1,         -- Siklus 1, 2, dst
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,                      -- Jatuh tempo (5 tahun)
    
    -- Target Baku Regulasi Kemenkes
    target_total_credits NUMERIC(5,2) NOT NULL DEFAULT 160.00,
    target_pembelajaran_credits NUMERIC(5,2) NOT NULL DEFAULT 72.00,  -- Min 45%
    target_pelayanan_credits NUMERIC(5,2) NOT NULL DEFAULT 56.00,      -- Min 35%
    target_pengabdian_credits NUMERIC(5,2) NOT NULL DEFAULT 8.00,       -- Min 5%
    target_flexibility_credits NUMERIC(5,2) NOT NULL DEFAULT 24.00,     -- Fleksibel 15%
    
    -- Akumulasi Terverifikasi (Diperbarui via Trigger / Transaksi Atomic)
    achieved_pembelajaran_credits NUMERIC(5,2) NOT NULL DEFAULT 0.00,
    achieved_pelayanan_credits NUMERIC(5,2) NOT NULL DEFAULT 0.00,
    achieved_pengabdian_credits NUMERIC(5,2) NOT NULL DEFAULT 0.00,
    achieved_total_credits NUMERIC(5,2) NOT NULL DEFAULT 0.00,
    
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_completed BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ----------------------------------------------------------------------------
-- 5. DOMAIN 4: KATALOG & BUKU LOG KEGIATAN SKP (Events & SKP Ledger)
-- ----------------------------------------------------------------------------

-- Kategori Ranah Kegiatan SKP
CREATE TABLE IF NOT EXISTS event_categories (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(150) NOT NULL UNIQUE,          -- Workshop USG, Simposium Fetomaternal, dll
    domain skp_domain_enum NOT NULL,             -- Pembelajaran, Pelayanan, Pengabdian
    default_credit_value NUMERIC(5,2) NOT NULL DEFAULT 1.00,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Katalog Event SKP Resmi POGI / Terakreditasi
CREATE TABLE IF NOT EXISTS skp_events (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    category_id UUID NOT NULL REFERENCES event_categories(id) ON DELETE RESTRICT,
    title VARCHAR(250) NOT NULL,
    organizer VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    credit_value NUMERIC(5,2) NOT NULL CHECK (credit_value > 0),
    kemenkes_accreditation_number VARCHAR(100),
    plataran_sehat_course_id VARCHAR(100),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Buku Log Entri Perolehan SKP Anggota
CREATE TABLE IF NOT EXISTS skp_entries (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    doctor_id UUID NOT NULL REFERENCES doctors(id) ON DELETE CASCADE,
    period_id UUID NOT NULL REFERENCES doctor_skp_periods(id) ON DELETE CASCADE,
    event_id UUID REFERENCES skp_events(id) ON DELETE SET NULL,
    category_id UUID NOT NULL REFERENCES event_categories(id) ON DELETE RESTRICT,
    domain skp_domain_enum NOT NULL,
    title VARCHAR(250) NOT NULL,
    activity_date DATE NOT NULL,
    credit_amount NUMERIC(5,2) NOT NULL CHECK (credit_amount >= 0),
    status skp_entry_status_enum NOT NULL DEFAULT 'NOT_SUBMITTED',
    verified_by_user_id UUID REFERENCES users(id) ON DELETE SET NULL, -- Admin POGI / Cabang
    verified_at TIMESTAMPTZ,
    rejection_reason TEXT,
    kemenkes_log_reference VARCHAR(100),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ----------------------------------------------------------------------------
-- 6. DOMAIN 5: REPOSITORI BERKAS BUKTI DIGITAL (Document Vault)
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS doctor_documents (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    doctor_id UUID NOT NULL REFERENCES doctors(id) ON DELETE CASCADE,
    skp_entry_id UUID REFERENCES skp_entries(id) ON DELETE SET NULL,
    document_type document_type_enum NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    
    -- MANDATORI: Tautan Google Drive Berkas Bukti (Pencegahan Pembengkakan Storage Server)
    google_drive_url TEXT NOT NULL,
    
    -- OPSIONAL: Upload biner lokal/S3 jika diaktifkan (Default NULL)
    file_size_bytes BIGINT CHECK (file_size_bytes IS NULL OR file_size_bytes <= 10485760), -- Opsional Max 10MB
    mime_type VARCHAR(100) DEFAULT 'application/pdf',
    storage_bucket VARCHAR(100) DEFAULT 'google-drive',
    storage_object_key VARCHAR(500), -- Opsional jika disimpan di S3 / Cloud Storage
    sha256_checksum CHAR(64),        -- Opsional Deduplikasi & Integritas Dokumen
    
    is_verified BOOLEAN NOT NULL DEFAULT FALSE,
    verified_by_user_id UUID REFERENCES users(id) ON DELETE SET NULL,
    verified_at TIMESTAMPTZ,
    uploaded_by_user_id UUID NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ
);

-- ----------------------------------------------------------------------------
-- 7. DOMAIN 6: SNAPSHOT CUTOFF BULANAN & ARSIP LAPORAN (Monthly Reports & Invoices)
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS monthly_cutoff_snapshots (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    period_year INT NOT NULL CHECK (period_year >= 2024),
    period_month INT NOT NULL CHECK (period_month BETWEEN 1 AND 12),
    cutoff_timestamp TIMESTAMPTZ NOT NULL,       -- Eksekusi otomatis Tgl 1, 00:00 WIB
    total_doctors_monitored INT NOT NULL,
    total_complete_doctors INT NOT NULL,
    total_incomplete_doctors INT NOT NULL,
    total_skp_verified NUMERIC(10,2) NOT NULL,
    total_skp_pending NUMERIC(10,2) NOT NULL,
    pdf_storage_key VARCHAR(500) NOT NULL,       -- PDF Laporan Resmi Tersimpan Permanen
    pdf_sha256 CHAR(64) NOT NULL,
    pdf_file_size_bytes BIGINT NOT NULL,
    is_locked BOOLEAN NOT NULL DEFAULT TRUE,     -- Immutable Audit Snapshot
    generated_by_user_id UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (period_year, period_month)
);

-- Rincian Snapshot Per Dokter (Menjamin lookup instan histori tanpa rekalkulasi ratusan entri)
CREATE TABLE IF NOT EXISTS monthly_doctor_cutoff_details (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    snapshot_id UUID NOT NULL REFERENCES monthly_cutoff_snapshots(id) ON DELETE CASCADE,
    doctor_id UUID NOT NULL REFERENCES doctors(id) ON DELETE CASCADE,
    branch_id UUID NOT NULL REFERENCES pogi_branches(id) ON DELETE RESTRICT,
    total_achieved_credits NUMERIC(5,2) NOT NULL,
    status doctor_compliance_status_enum NOT NULL,
    pembelajaran_credits NUMERIC(5,2) NOT NULL,
    pelayanan_credits NUMERIC(5,2) NOT NULL,
    pengabdian_credits NUMERIC(5,2) NOT NULL,
    UNIQUE (snapshot_id, doctor_id)
);

-- ----------------------------------------------------------------------------
-- 8. DOMAIN 7: ANTREAN NOTIFIKASI & OTOMASI WHATSAPP BLAST (Message Queue & Rate Limiting)
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS wa_blast_campaigns (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    title VARCHAR(200) NOT NULL,
    trigger_type wa_trigger_type_enum NOT NULL,
    template_type VARCHAR(50) NOT NULL,          -- CUTOFF_H7, CUTOFF_H2, MISSING_DOCS, dll
    message_template TEXT NOT NULL,
    target_filters JSONB NOT NULL DEFAULT '{}',  -- e.g. {"branch_id": "...", "status": "INCOMPLETE"}
    total_recipients INT NOT NULL DEFAULT 0,
    successful_sends INT NOT NULL DEFAULT 0,
    failed_sends INT NOT NULL DEFAULT 0,
    created_by_user_id UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS wa_message_queue (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    campaign_id UUID REFERENCES wa_blast_campaigns(id) ON DELETE CASCADE,
    doctor_id UUID NOT NULL REFERENCES doctors(id) ON DELETE CASCADE,
    recipient_phone VARCHAR(30) NOT NULL,
    recipient_name VARCHAR(200) NOT NULL,
    message_body TEXT NOT NULL,
    status wa_queue_status_enum NOT NULL DEFAULT 'QUEUED',
    retry_count INT NOT NULL DEFAULT 0,
    jitter_ms INT NOT NULL DEFAULT 1500,         -- Perlindungan Anti-Ban (Delay acak per pesan)
    scheduled_at TIMESTAMPTZ NOT NULL,
    sent_at TIMESTAMPTZ,
    failure_reason TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ----------------------------------------------------------------------------
-- 9. DOMAIN 8: AUDIT LOGS & MONITORING PERFORMA VERIFIKATOR (SLA Tracker Engine)
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS audit_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(100) NOT NULL,                -- e.g. 'APPROVE_CREDITS', 'REASSIGN_PIC'
    entity_name VARCHAR(100) NOT NULL,           -- e.g. 'doctors', 'skp_entries'
    entity_id UUID NOT NULL,
    old_values JSONB,
    new_values JSONB,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Catatan Kecepatan & SLA Verifikator (Untuk modul Admin Performance & SLA Tracker)
CREATE TABLE IF NOT EXISTS verifier_performance_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    verifier_user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    doctor_id UUID REFERENCES doctors(id) ON DELETE CASCADE,
    skp_entry_id UUID REFERENCES skp_entries(id) ON DELETE SET NULL,
    document_id UUID REFERENCES doctor_documents(id) ON DELETE SET NULL,
    action_type VARCHAR(50) NOT NULL,            -- 'VERIFIED', 'REJECTED'
    turnaround_time_seconds INT,                 -- Selisih waktu dari berkas masuk ke pengesahan
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ============================================================================
-- 10. INDEXING STRATEGY: ZERO-FULLSCAN DATA OPTIMIZATION
-- ============================================================================
-- Menghilangkan Sequential Scan (Full Table Scan) pada seluruh query relasional,
-- filter pencarian, agregasi geospasial, antrean worker, serta pelaporan bulanan.

-- A. Foreign Key Indexes (Mencegah Full Table Scan saat JOIN)
CREATE INDEX IF NOT EXISTS idx_pogi_branches_prov ON pogi_branches (province_id);
CREATE INDEX IF NOT EXISTS idx_institutions_prov ON institutions (province_id);
CREATE INDEX IF NOT EXISTS idx_institutions_branch ON institutions (branch_id);
CREATE INDEX IF NOT EXISTS idx_users_branch ON users (branch_id) WHERE deleted_at IS NULL;
CREATE INDEX IF NOT EXISTS idx_users_role ON users (role) WHERE deleted_at IS NULL;
CREATE INDEX IF NOT EXISTS idx_doctor_add_inst_doc ON doctor_additional_institutions (doctor_id);
CREATE INDEX IF NOT EXISTS idx_doctor_add_inst_inst ON doctor_additional_institutions (institution_id);

-- B. Composite & Partial Indexes untuk Filter Direktori Dokter (doctors.html)
-- Query 1: Filter dokter per Cabang & Status Kepatuhan (Admin Cabang Isolasi Data)
CREATE INDEX IF NOT EXISTS idx_doctors_branch_status 
ON doctors (branch_id, status) 
WHERE deleted_at IS NULL;

-- Query 2: Filter dokter per Provinsi & Status Kepatuhan (Peta 38 Provinsi & Nasional)
CREATE INDEX IF NOT EXISTS idx_doctors_prov_status 
ON doctors (province_id, status) 
WHERE deleted_at IS NULL;

-- Query 3: Filter dokter per PIC Admin POGI & Status Kepatuhan (Monitoring Dokter Binaan)
CREATE INDEX IF NOT EXISTS idx_doctors_pic_status 
ON doctors (assigned_pic_id, status) 
WHERE deleted_at IS NULL;

-- Query 4: Dual-Timestamp Sync Monitoring (Cari dokter yang belum disinkronkan ke Kemenkes)
CREATE INDEX IF NOT EXISTS idx_doctors_sync_monitoring 
ON doctors (dashboard_updated_at DESC, kemenkes_updated_at) 
WHERE deleted_at IS NULL;

-- C. Full-Text & Trigram GIN Search Indexes (Pencarian Instan Tanpa Seq Scan)
-- Mendukung pencarian cepat: Nama dokter, No NPA POGI, No STR
CREATE INDEX IF NOT EXISTS idx_doctors_trgm_search 
ON doctors USING gin (
    full_name gin_trgm_ops,
    npa_pogi gin_trgm_ops,
    str_number gin_trgm_ops
);

-- D. Composite Indexes untuk Transaksi & Rekalkulasi SKP (skp_entries)
-- Query 5: Rincian SKP Dokter per Periode & Ranah dengan Covering Indeks
CREATE INDEX IF NOT EXISTS idx_skp_entries_doc_period_domain 
ON skp_entries (doctor_id, period_id, domain, status) 
INCLUDE (credit_amount, activity_date);

-- Query 6: Entri pending verifikasi untuk antrean verifikasi Admin POGI
CREATE INDEX IF NOT EXISTS idx_skp_entries_pending 
ON skp_entries (status, activity_date ASC) 
WHERE status = 'NOT_SUBMITTED';

-- Query 7: Periode aktif dokter
CREATE UNIQUE INDEX IF NOT EXISTS idx_doctor_active_period 
ON doctor_skp_periods (doctor_id) 
WHERE is_active = TRUE;

-- E. Document Vault Indexes (doctor_documents)
-- Query 8: Berkas per dokter terkelompok berdasarkan tipe dokumen
CREATE INDEX IF NOT EXISTS idx_docs_doctor_type 
ON doctor_documents (doctor_id, document_type, created_at DESC) 
WHERE deleted_at IS NULL;

-- Query 9: Deduplikasi berkas via SHA256
CREATE INDEX IF NOT EXISTS idx_docs_sha256 
ON doctor_documents (sha256_checksum);

-- F. Antrean Scheduler & WhatsApp Blast (wa_message_queue)
-- Query 10: Pengambilan antrean WA siap kirim oleh Worker BullMQ / cron
CREATE INDEX IF NOT EXISTS idx_wa_queue_processing 
ON wa_message_queue (status, scheduled_at ASC) 
WHERE status = 'QUEUED';

-- G. Audit Trail & Performance Indexing
CREATE INDEX IF NOT EXISTS idx_audit_logs_entity 
ON audit_logs (entity_name, entity_id, created_at DESC);

CREATE INDEX IF NOT EXISTS idx_audit_logs_user 
ON audit_logs (user_id, created_at DESC);

CREATE INDEX IF NOT EXISTS idx_verifier_perf_user_date 
ON verifier_performance_logs (verifier_user_id, created_at DESC) 
INCLUDE (turnaround_time_seconds, action_type);

-- ============================================================================
-- 11. MATERIALIZED VIEWS UNTUK DASHBOARD EKSEKUTIF REAL-TIME
-- ============================================================================
-- Menghilangkan agregasi berulang jutaan baris pada halaman utama (dashboard.html & provinces.html).

CREATE MATERIALIZED VIEW IF NOT EXISTS mv_province_compliance_summary AS
SELECT 
    p.id AS province_id,
    p.name AS province_name,
    p.region_island,
    COUNT(d.id) AS total_doctors,
    COUNT(d.id) FILTER (WHERE d.status = 'COMPLETE') AS total_complete,
    COUNT(d.id) FILTER (WHERE d.status = 'INCOMPLETE') AS total_incomplete,
    ROUND(
        COALESCE(
            (COUNT(d.id) FILTER (WHERE d.status = 'COMPLETE')::NUMERIC / NULLIF(COUNT(d.id), 0)) * 100,
            0
        ), 1
    ) AS compliance_rate_pct,
    -- Klasifikasi Warna Peta Interaktif 38 Provinsi (RULES.md Bab 4)
    CASE 
        WHEN (COUNT(d.id) FILTER (WHERE d.status = 'COMPLETE')::NUMERIC / NULLIF(COUNT(d.id), 0)) * 100 >= 85.0 THEN 'KUNING_AMAN'
        WHEN (COUNT(d.id) FILTER (WHERE d.status = 'COMPLETE')::NUMERIC / NULLIF(COUNT(d.id), 0)) * 100 >= 50.0 THEN 'HIJAU_WASPADA'
        ELSE 'MERAH_KRITIS'
    END AS status_color_code,
    COALESCE(SUM(p_skp.achieved_total_credits), 0) AS total_achieved_credits,
    NOW() AS refreshed_at
FROM provinces p
LEFT JOIN doctors d ON p.id = d.province_id AND d.deleted_at IS NULL
LEFT JOIN doctor_skp_periods p_skp ON d.id = p_skp.doctor_id AND p_skp.is_active = TRUE
GROUP BY p.id, p.name, p.region_island;

-- Unique Index pada Materialized View untuk mendukung REFRESH CONCURRENTLY
CREATE UNIQUE INDEX IF NOT EXISTS idx_mv_prov_summary_id ON mv_province_compliance_summary (province_id);

-- ============================================================================
-- 12. TRIGGERS & BUSINESS LOGIC ENFORCEMENT
-- ============================================================================

-- A. Trigger Pembaruan Kolom updated_at Otomatis
CREATE OR REPLACE FUNCTION trigger_set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DO $$ BEGIN
    CREATE TRIGGER trg_pogi_branches_updated BEFORE UPDATE ON pogi_branches FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at();
    CREATE TRIGGER trg_institutions_updated BEFORE UPDATE ON institutions FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at();
    CREATE TRIGGER trg_users_updated BEFORE UPDATE ON users FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at();
    CREATE TRIGGER trg_doctors_updated BEFORE UPDATE ON doctors FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at();
    CREATE TRIGGER trg_doctor_periods_updated BEFORE UPDATE ON doctor_skp_periods FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at();
    CREATE TRIGGER trg_skp_entries_updated BEFORE UPDATE ON skp_entries FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at();
EXCEPTION WHEN duplicate_object THEN null; END $$;

-- B. Trigger Rekalkulasi SKP Terverifikasi & Sinkronisasi Status Dokter
CREATE OR REPLACE FUNCTION trigger_recalculate_doctor_skp()
RETURNS TRIGGER AS $$
DECLARE
    v_period_id UUID;
    v_doctor_id UUID;
    v_total_pemb NUMERIC(5,2);
    v_total_pel NUMERIC(5,2);
    v_total_peng NUMERIC(5,2);
    v_total NUMERIC(5,2);
    v_target_total NUMERIC(5,2);
BEGIN
    v_period_id := COALESCE(NEW.period_id, OLD.period_id);
    v_doctor_id := COALESCE(NEW.doctor_id, OLD.doctor_id);

    -- Hitung total kredit terverifikasi (status = 'DONE' atau 'SUBMITTED')
    SELECT 
        COALESCE(SUM(credit_amount) FILTER (WHERE domain = 'PEMBELAJARAN'), 0),
        COALESCE(SUM(credit_amount) FILTER (WHERE domain = 'PELAYANAN'), 0),
        COALESCE(SUM(credit_amount) FILTER (WHERE domain = 'PENGABDIAN'), 0),
        COALESCE(SUM(credit_amount), 0)
    INTO v_total_pemb, v_total_pel, v_total_peng, v_total
    FROM skp_entries
    WHERE period_id = v_period_id AND status IN ('DONE', 'SUBMITTED');

    -- Dapatkan target total periode
    SELECT target_total_credits INTO v_target_total 
    FROM doctor_skp_periods WHERE id = v_period_id;

    -- Update akumulasi pada periode aktif
    UPDATE doctor_skp_periods
    SET achieved_pembelajaran_credits = v_total_pemb,
        achieved_pelayanan_credits = v_total_pel,
        achieved_pengabdian_credits = v_total_peng,
        achieved_total_credits = v_total,
        is_completed = (v_total >= COALESCE(v_target_total, 160.00)),
        updated_at = NOW()
    WHERE id = v_period_id;

    -- Sinkronisasi status dokter di tabel doctors
    UPDATE doctors
    SET status = CASE WHEN v_total >= COALESCE(v_target_total, 160.00) THEN 'COMPLETE'::doctor_compliance_status_enum ELSE 'INCOMPLETE'::doctor_compliance_status_enum END,
        dashboard_updated_at = NOW()
    WHERE id = v_doctor_id;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DO $$ BEGIN
    CREATE TRIGGER trg_after_skp_entries_mutation
    AFTER INSERT OR UPDATE OR DELETE ON skp_entries
    FOR EACH ROW EXECUTE FUNCTION trigger_recalculate_doctor_skp();
EXCEPTION WHEN duplicate_object THEN null; END $$;

-- ============================================================================
-- AKHIR SKEMA DDL DATABASE POGI SKP TRACKER
-- ============================================================================
