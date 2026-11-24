<?php

namespace App\Infrastructure\Persistence;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

class PogiUserProvider extends EloquentUserProvider
{
    /**
     * Ambil user berdasarkan identifier (ID) langsung dari basis data
     */
    public function retrieveById($identifier): ?Authenticatable
    {
        try {
            return parent::retrieveById($identifier);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Ambil user berdasarkan kredensial (Email) langsung dari basis data
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        try {
            return parent::retrieveByCredentials($credentials);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Validasi kecocokan kata sandi menggunakan Hash::check standar
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $plain = $credentials['password'] ?? '';
        if (empty($plain)) {
            return false;
        }

        try {
            return Hash::check($plain, $user->getAuthPassword());
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Simpan remember token secara aman
     */
    public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token): void
    {
        try {
            parent::updateRememberToken($user, $token);
        } catch (\Throwable $e) {
            // Abaikan jika database memiliki kendala penyimpanan token
        }
    }
}

