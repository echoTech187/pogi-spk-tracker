<?php

namespace App\Infrastructure\Security;

/**
 * Layanan enkripsi AES-256-GCM & HMAC-SHA256 deterministik untuk NIK (UU PDP No. 27/2022).
 */
class NikCipher
{
    private const CIPHER = 'aes-256-gcm';

    /**
     * Ambil kunci 32-byte dari APP_KEY / ENCRYPTION_MASTER_KEY.
     */
    private static function key(): string
    {
        $raw = (string) (env('ENCRYPTION_MASTER_KEY') ?: config('app.key', 'pogi-skp-tracker-default-key'));
        if (str_starts_with($raw, 'base64:')) {
            $decoded = base64_decode(substr($raw, 7), true);
            if ($decoded !== false) {
                $raw = $decoded;
            }
        }

        return hash('sha256', $raw, true);
    }

    /**
     * Enkripsi NIK menggunakan AES-256-GCM (IV 12-byte + Auth Tag 16-byte + Ciphertext).
     */
    public static function encrypt(string $nik): string
    {
        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt(trim($nik), self::CIPHER, self::key(), OPENSSL_RAW_DATA, $iv, $tag);

        if ($ciphertext === false) {
            throw new \RuntimeException('Gagal mengenkripsi NIK dengan AES-256-GCM.');
        }

        return 'GCM1:' . base64_encode($iv . $tag . $ciphertext);
    }

    /**
     * Dekripsi payload AES-256-GCM.
     */
    public static function decrypt(string $payload): ?string
    {
        if (!str_starts_with($payload, 'GCM1:')) {
            return null;
        }

        $bin = base64_decode(substr($payload, 5), true);
        if ($bin === false || strlen($bin) < 29) {
            return null;
        }

        $iv = substr($bin, 0, 12);
        $tag = substr($bin, 12, 16);
        $ciphertext = substr($bin, 28);

        $plain = openssl_decrypt($ciphertext, self::CIPHER, self::key(), OPENSSL_RAW_DATA, $iv, $tag);

        return $plain === false ? null : $plain;
    }

    /**
     * Hasilkan hash deterministik HMAC-SHA256 untuk lookup tanpa dekripsi (RULES.md #9.2).
     */
    public static function hash(string $nik): string
    {
        return hash_hmac('sha256', trim($nik), self::key());
    }
}
