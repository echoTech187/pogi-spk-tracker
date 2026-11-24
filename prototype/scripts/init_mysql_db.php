<?php

$host = '127.0.0.1';
$port = 3306;
$database = 'pogi_skp_tracker';
$username = 'root';
$password = 'root';

echo "1. Connecting to MySQL on port {$port}...\n";
$pdo = new PDO("mysql:host={$host};port={$port}", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec("DROP DATABASE IF EXISTS `{$database}`;");
$pdo->exec("CREATE DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$pdo->exec("USE `{$database}`;");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

echo "2. Creating tables in MySQL...\n";

$tables = [
    "CREATE TABLE IF NOT EXISTS provinces (
        id VARCHAR(10) PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        region_island VARCHAR(50) NOT NULL,
        latitude DECIMAL(9,6) NULL,
        longitude DECIMAL(9,6) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS pogi_branches (
        id CHAR(36) PRIMARY KEY,
        province_id VARCHAR(10) NOT NULL,
        branch_code VARCHAR(30) NOT NULL UNIQUE,
        branch_name VARCHAR(150) NOT NULL,
        secretariat_address TEXT NULL,
        contact_email VARCHAR(150) NULL,
        contact_phone VARCHAR(30) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_branches_province (province_id)
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS institutions (
        id CHAR(36) PRIMARY KEY,
        province_id VARCHAR(10) NOT NULL,
        branch_id CHAR(36) NULL,
        code VARCHAR(50) NULL UNIQUE,
        name VARCHAR(200) NOT NULL,
        institution_type VARCHAR(50) NOT NULL,
        hospital_class VARCHAR(10) NULL,
        address TEXT NULL,
        city VARCHAR(100) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_inst_province (province_id),
        INDEX idx_inst_branch (branch_id)
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS users (
        id CHAR(36) PRIMARY KEY,
        role ENUM('SUPER_ADMIN', 'ADMIN_PUSAT', 'ADMIN_POGI', 'ADMIN_CABANG', 'DOKTER') NOT NULL,
        full_name VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        phone_number VARCHAR(30) NULL,
        password_hash VARCHAR(255) NOT NULL,
        branch_id CHAR(36) NULL,
        is_active BOOLEAN NOT NULL DEFAULT TRUE,
        remember_token VARCHAR(100) NULL,
        email_verified_at TIMESTAMP NULL,
        last_login_at TIMESTAMP NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL,
        INDEX idx_users_role (role),
        INDEX idx_users_branch (branch_id)
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS doctors (
        id CHAR(36) PRIMARY KEY,
        user_id CHAR(36) NULL,
        npa_pogi VARCHAR(50) NOT NULL UNIQUE,
        full_name VARCHAR(200) NOT NULL,
        specialization_title VARCHAR(100) NOT NULL DEFAULT 'Sp.OG',
        nik_encrypted BLOB NULL,
        nik_hash VARCHAR(64) NULL,
        str_number VARCHAR(50) NOT NULL UNIQUE,
        sip_number VARCHAR(50) NULL,
        sip_expiry_date DATE NOT NULL,
        email VARCHAR(150) NULL,
        whatsapp_number VARCHAR(30) NOT NULL,
        branch_id CHAR(36) NOT NULL,
        province_id VARCHAR(10) NOT NULL,
        primary_institution_id CHAR(36) NULL,
        assigned_pic_id CHAR(36) NULL,
        plataran_sehat_username VARCHAR(100) NULL,
        plataran_sehat_pass_encrypted BLOB NULL,
        status ENUM('COMPLETE', 'INCOMPLETE', 'PENDING_REVIEW') NOT NULL DEFAULT 'INCOMPLETE',
        dashboard_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        kemenkes_updated_at TIMESTAMP NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL,
        INDEX idx_doctors_user (user_id),
        INDEX idx_doctors_branch (branch_id),
        INDEX idx_doctors_province (province_id),
        INDEX idx_doctors_status (status)
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS doctor_skp_periods (
        id CHAR(36) PRIMARY KEY,
        doctor_id CHAR(36) NOT NULL,
        cycle_number INT NOT NULL DEFAULT 1,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        target_total_credits DECIMAL(5,2) NOT NULL DEFAULT 0.00,
        target_pembelajaran_credits DECIMAL(5,2) NOT NULL DEFAULT 0.00,
        target_pelayanan_credits DECIMAL(5,2) NOT NULL DEFAULT 0.00,
        target_pengabdian_credits DECIMAL(5,2) NOT NULL DEFAULT 0.00,
        target_flexibility_credits DECIMAL(5,2) NOT NULL DEFAULT 0.00,
        achieved_pembelajaran_credits DECIMAL(5,2) NOT NULL DEFAULT 0.00,
        achieved_pelayanan_credits DECIMAL(5,2) NOT NULL DEFAULT 0.00,
        achieved_pengabdian_credits DECIMAL(5,2) NOT NULL DEFAULT 0.00,
        achieved_total_credits DECIMAL(5,2) NOT NULL DEFAULT 0.00,
        is_active BOOLEAN NOT NULL DEFAULT TRUE,
        is_completed BOOLEAN NOT NULL DEFAULT FALSE,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_periods_doctor (doctor_id),
        INDEX idx_periods_active (is_active)
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS event_categories (
        id CHAR(36) PRIMARY KEY,
        name VARCHAR(150) NOT NULL UNIQUE,
        domain ENUM('PEMBELAJARAN', 'PELAYANAN', 'PENGABDIAN', 'PUBLIKASI_ILMIAH') NOT NULL,
        default_credit_value DECIMAL(5,2) NOT NULL DEFAULT 0.00,
        description TEXT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS skp_events (
        id CHAR(36) PRIMARY KEY,
        category_id CHAR(36) NOT NULL,
        title VARCHAR(250) NOT NULL,
        organizer VARCHAR(150) NOT NULL,
        event_date DATE NOT NULL,
        credit_value DECIMAL(5,2) NOT NULL,
        kemenkes_accreditation_number VARCHAR(100) NULL,
        plataran_sehat_course_id VARCHAR(100) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_events_category (category_id)
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS skp_entries (
        id CHAR(36) PRIMARY KEY,
        doctor_id CHAR(36) NOT NULL,
        period_id CHAR(36) NOT NULL,
        event_id CHAR(36) NULL,
        category_id CHAR(36) NOT NULL,
        domain ENUM('PEMBELAJARAN', 'PELAYANAN', 'PENGABDIAN', 'PUBLIKASI_ILMIAH') NOT NULL,
        title VARCHAR(250) NOT NULL,
        activity_date DATE NOT NULL,
        credit_amount DECIMAL(5,2) NOT NULL,
        status ENUM('NOT_SUBMITTED', 'SUBMITTED', 'DONE', 'REJECTED') NOT NULL DEFAULT 'NOT_SUBMITTED',
        verified_by_user_id CHAR(36) NULL,
        verified_at TIMESTAMP NULL,
        rejection_reason TEXT NULL,
        kemenkes_log_reference VARCHAR(100) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_entries_doctor (doctor_id),
        INDEX idx_entries_period (period_id),
        INDEX idx_entries_status (status)
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS doctor_documents (
        id CHAR(36) PRIMARY KEY,
        doctor_id CHAR(36) NOT NULL,
        skp_entry_id CHAR(36) NULL,
        document_type VARCHAR(50) NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        google_drive_url TEXT NOT NULL,
        file_size_bytes BIGINT NULL,
        mime_type VARCHAR(100) DEFAULT 'application/pdf',
        storage_bucket VARCHAR(100) DEFAULT 'google-drive',
        storage_object_key VARCHAR(500) NULL,
        sha256_checksum CHAR(64) NULL,
        is_verified BOOLEAN NOT NULL DEFAULT FALSE,
        verified_by_user_id CHAR(36) NULL,
        verified_at TIMESTAMP NULL,
        uploaded_by_user_id CHAR(36) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL,
        INDEX idx_docs_doctor (doctor_id)
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS monthly_cutoff_snapshots (
        id CHAR(36) PRIMARY KEY,
        period_year INT NOT NULL,
        period_month INT NOT NULL,
        cutoff_timestamp TIMESTAMP NOT NULL,
        total_doctors_monitored INT NOT NULL,
        total_complete_doctors INT NOT NULL,
        total_incomplete_doctors INT NOT NULL,
        total_skp_verified DECIMAL(10,2) NOT NULL,
        total_skp_pending DECIMAL(10,2) NOT NULL,
        pdf_storage_key VARCHAR(500) NOT NULL,
        pdf_sha256 CHAR(64) NOT NULL,
        pdf_file_size_bytes BIGINT NOT NULL,
        is_locked BOOLEAN NOT NULL DEFAULT TRUE,
        generated_by_user_id CHAR(36) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS monthly_doctor_cutoff_details (
        id CHAR(36) PRIMARY KEY,
        snapshot_id CHAR(36) NOT NULL,
        doctor_id CHAR(36) NOT NULL,
        branch_id CHAR(36) NOT NULL,
        total_achieved_credits DECIMAL(5,2) NOT NULL,
        status VARCHAR(50) NOT NULL,
        pembelajaran_credits DECIMAL(5,2) NOT NULL,
        pelayanan_credits DECIMAL(5,2) NOT NULL,
        pengabdian_credits DECIMAL(5,2) NOT NULL,
        INDEX idx_cutoff_snapshot (snapshot_id),
        INDEX idx_cutoff_doctor (doctor_id)
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS wa_blast_campaigns (
        id CHAR(36) PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        trigger_type VARCHAR(50) NOT NULL,
        template_type VARCHAR(50) NOT NULL,
        message_template TEXT NOT NULL,
        target_filters JSON NULL,
        total_recipients INT NOT NULL DEFAULT 0,
        successful_sends INT NOT NULL DEFAULT 0,
        failed_sends INT NOT NULL DEFAULT 0,
        created_by_user_id CHAR(36) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS wa_message_queue (
        id CHAR(36) PRIMARY KEY,
        campaign_id CHAR(36) NULL,
        doctor_id CHAR(36) NOT NULL,
        recipient_phone VARCHAR(30) NOT NULL,
        recipient_name VARCHAR(200) NOT NULL,
        message_body TEXT NOT NULL,
        status VARCHAR(50) NOT NULL DEFAULT 'QUEUED',
        retry_count INT NOT NULL DEFAULT 0,
        jitter_ms INT NOT NULL DEFAULT 0,
        scheduled_at TIMESTAMP NOT NULL,
        sent_at TIMESTAMP NULL,
        failure_reason TEXT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_queue_doctor (doctor_id)
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS audit_logs (
        id CHAR(36) PRIMARY KEY,
        user_id CHAR(36) NULL,
        action VARCHAR(100) NOT NULL,
        entity_name VARCHAR(100) NOT NULL,
        entity_id CHAR(36) NOT NULL,
        old_values JSON NULL,
        new_values JSON NULL,
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_audit_user (user_id)
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS verifier_performance_logs (
        id CHAR(36) PRIMARY KEY,
        verifier_user_id CHAR(36) NOT NULL,
        doctor_id CHAR(36) NULL,
        skp_entry_id CHAR(36) NULL,
        document_id CHAR(36) NULL,
        action_type VARCHAR(50) NOT NULL,
        turnaround_time_seconds INT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_perf_verifier (verifier_user_id)
    ) ENGINE=InnoDB;"
];

foreach ($tables as $sql) {
    $pdo->exec($sql);
}

echo "3. Tables created successfully. Transforming seeds.sql for MySQL...\n";

$seedsPath = file_exists(__DIR__ . '/../../pogi-backend/database/sql/seeds.sql')
    ? __DIR__ . '/../../pogi-backend/database/sql/seeds.sql'
    : (file_exists(__DIR__ . '/../seeds.sql') ? __DIR__ . '/../seeds.sql' : __DIR__ . '/../database/seeds.sql');
$sqlContent = file_get_contents($seedsPath);

// Normalize line endings
$sqlContent = str_replace(["\r\n", "\r"], "\n", $sqlContent);

// Strip comments lines that start with --
$lines = explode("\n", $sqlContent);
$cleanLines = [];
foreach ($lines as $line) {
    $trimmed = trim($line);
    if (!str_starts_with($trimmed, '--')) {
        $cleanLines[] = $line;
    }
}
$sqlContent = implode("\n", $cleanLines);

// Replacements for MySQL compatibility
$sqlContent = preg_replace("/'([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})\+[0-9]{2}'/", "'$1'", $sqlContent);
$sqlContent = preg_replace("/'\\\\x([0-9a-fA-F]+)'::bytea/", "UNHEX('$1')", $sqlContent);
$sqlContent = preg_replace("/'0812'\\s*\\|\\|\\s*lpad\\(([^,]+),\\s*([^,]+),\\s*'([^']+)'\\)/", "CONCAT('0812', LPAD($1, $2, '$3'))", $sqlContent);
$sqlContent = preg_replace_callback("/interval\s*'([0-9]+)\s*(day|days|hour|hours)'/i", function($m) {
    $num = $m[1];
    $unit = (stripos($m[2], 'hour') !== false) ? 'HOUR' : 'DAY';
    return "INTERVAL {$num} {$unit}";
}, $sqlContent);

$sqlContent = preg_replace("/REFRESH MATERIALIZED VIEW[^;]+;/i", "", $sqlContent);
$sqlContent = preg_replace("/SET session_replication_role[^;]+;/i", "", $sqlContent);
$sqlContent = preg_replace("/BEGIN;/i", "", $sqlContent);
$sqlContent = preg_replace("/COMMIT;/i", "", $sqlContent);
$sqlContent = preg_replace("/ON CONFLICT\s*\([^)]+\)\s*DO NOTHING/i", "ON DUPLICATE KEY UPDATE id=id", $sqlContent);
$sqlContent = preg_replace_callback("/ON CONFLICT\s*\([^)]+\)\s*DO UPDATE SET\s*([^;]+);/i", function($m) {
    $updates = $m[1];
    $converted = preg_replace("/EXCLUDED\.([a-zA-Z0-9_]+)/", "VALUES($1)", $updates);
    return "ON DUPLICATE KEY UPDATE " . $converted . ";";
}, $sqlContent);

echo "4. Executing seed statements into MySQL...\n";
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

// Split statements by semicolon followed by newline
$statements = array_filter(array_map('trim', explode(";\n", $sqlContent)));

$executed = 0;
foreach ($statements as $stmt) {
    $trimmedStmt = trim($stmt, " \t\n\r;");
    if (!empty($trimmedStmt)) {
        try {
            $pdo->exec($trimmedStmt);
            $executed++;
        } catch (Exception $e) {
            echo "Notice on stmt: " . substr($trimmedStmt, 0, 50) . "... Msg: " . $e->getMessage() . "\n";
        }
    }
}

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
echo "5. Executed {$executed} seed statements successfully!\n";

$docCount = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
$provCount = $pdo->query("SELECT COUNT(*) FROM provinces")->fetchColumn();
$branchCount = $pdo->query("SELECT COUNT(*) FROM pogi_branches")->fetchColumn();
$periodCount = $pdo->query("SELECT COUNT(*) FROM doctor_skp_periods")->fetchColumn();
$entryCount = $pdo->query("SELECT COUNT(*) FROM skp_entries")->fetchColumn();
$dkiBranchId = $pdo->query("SELECT id FROM pogi_branches WHERE branch_code = 'POGI-31'")->fetchColumn();
$dkiCount = $pdo->query("SELECT COUNT(*) FROM doctors WHERE branch_id = '{$dkiBranchId}'")->fetchColumn();

echo "====================================================\n";
echo " VERIFIKASI DATA MYSQL (pogi_skp_tracker):\n";
echo " - 38 Provinces: {$provCount}\n";
echo " - 38 POGI Branches: {$branchCount}\n";
echo " - Doctors Total: {$docCount}\n";
echo " - Doctors DKI Jakarta: {$dkiCount}\n";
echo " - Active SKP Periods: {$periodCount}\n";
echo " - SKP Entries: {$entryCount}\n";
echo "====================================================\n";
