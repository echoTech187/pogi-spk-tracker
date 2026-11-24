<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;
use Exception;

class InitPogiDatabaseCommand extends Command
{
    protected $signature = 'pogi:init-db {--seed : Jalankan seeder SQL setelah inisialisasi skema}';
    protected $description = 'Inisialisasi skema database PostgreSQL POGI SKP Tracker dan seeder';

    public function handle(): int
    {
        $this->info('====================================================');
        $this->info(' INISIALISASI DATABASE POGI SKP TRACKER (PostgreSQL)');
        $this->info('====================================================');

        $host = config('database.connections.pgsql.host', '127.0.0.1');
        $port = config('database.connections.pgsql.port', '5433');
        $database = config('database.connections.pgsql.database', 'pogi_skp_tracker');
        $username = config('database.connections.pgsql.username', 'postgres');
        $password = config('database.connections.pgsql.password', '');

        $this->info("Menghubungkan ke PostgreSQL: {$username}@{$host}:{$port} ...");

        try {
            // 1. Cek & Buat Database jika belum ada
            $adminPdo = new PDO(
                "pgsql:host={$host};port={$port};dbname=postgres",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $stmt = $adminPdo->query("SELECT 1 FROM pg_database WHERE datname = '{$database}'");
            if (!$stmt->fetch()) {
                $this->info("Membuat database baru: {$database} ...");
                $adminPdo->exec("CREATE DATABASE \"{$database}\" WITH ENCODING 'UTF8'");
                $this->info("Database {$database} berhasil dibuat.");
            } else {
                $this->info("Database {$database} sudah tersedia.");
            }
        } catch (Exception $e) {
            $this->warn("Catatan koneksi postgres root: " . $e->getMessage());
        }

        // 2. Eksekusi Schema SQL
        $schemaPath = database_path('sql/schema.sql');
        if (!File::exists($schemaPath)) {
            $this->error("File skema tidak ditemukan di: {$schemaPath}");
            return 1;
        }

        $this->info("Mengeksekusi skema database (18 Tabel, Triggers, Functions, & Materialized Views)...");
        try {
            $dbPdo = new PDO(
                "pgsql:host={$host};port={$port};dbname={$database}",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $schemaSql = File::get($schemaPath);
            $dbPdo->exec($schemaSql);
            $this->info("✓ Skema tabel, tipe enum, triggers, dan indeks Zero-Fullscan berhasil diinisialisasi.");

            // 3. Eksekusi Seeder SQL jika flag --seed disertakan atau konfirmasi user
            if ($this->option('seed') || $this->confirm('Apakah Anda ingin langsung menjalankan seeder data awal?', true)) {
                $seedsPath = database_path('sql/seeds.sql');
                if (File::exists($seedsPath)) {
                    $this->info("Mengeksekusi data awal seeder (38 Provinsi, 38 Cabang, 200 Dokter, 670 SKP entries, Document Vault)...");
                    $seedsSql = File::get($seedsPath);
                    $dbPdo->exec($seedsSql);
                    $this->info("✓ Seluruh data seeder berhasil dimuat ke database tanpa error!");
                } else {
                    $this->warn("File seeds.sql tidak ditemukan di: {$seedsPath}");
                }
            }

            $this->info("====================================================");
            $this->info(" DATABASE POGI SKP TRACKER SIAP DIGUNAKAN!");
            $this->info("====================================================");
            return 0;

        } catch (Exception $e) {
            $this->error("Gagal menginisialisasi database: " . $e->getMessage());
            $this->line("Pastikan konfigurasi DB_PASSWORD di file .env sudah sesuai dengan kredensial PostgreSQL Anda.");
            return 1;
        }
    }
}
