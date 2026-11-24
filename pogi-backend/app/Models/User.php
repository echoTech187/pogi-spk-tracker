<?php

namespace App\Models;

use App\Domain\Entities\SystemRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids, SoftDeletes;

    protected $table = 'users';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Atribut yang dapat diisi massal
     */
    protected $fillable = [
        'id',
        'role',
        'full_name',
        'email',
        'phone_number',
        'password_hash',
        'password',
        'branch_id',
        'is_active',
        'email_verified_at',
        'last_login_at',
    ];

    /**
     * Atribut yang disembunyikan dalam serialisasi JSON
     */
    protected $hidden = [
        'password_hash',
        'password',
        'remember_token',
    ];

    /**
     * Konversi tipe data
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'is_active'         => 'boolean',
            'created_at'        => 'datetime',
            'updated_at'        => 'datetime',
            'deleted_at'        => 'datetime',
        ];
    }

    /**
     * Resolver password untuk autentikasi Laravel Guard
     */
    public function getAuthPassword()
    {
        return $this->password_hash ?? $this->password;
    }

    /**
     * Nama pengguna untuk kompatibilitas tampilan
     */
    public function getNameAttribute(): string
    {
        return $this->full_name ?? 'Pengurus POGI';
    }

    // =========================================================================
    // METODE PEMERIKSAAN RBAC (5 PERAN RESMI SISTEM)
    // =========================================================================

    public function hasRole(string|array $roles): bool
    {
        if ($this->isSuperAdmin()) {
            return true; // Super Admin memiliki wewenang penuh
        }

        $userRole = strtoupper((string) $this->role);
        $roles = is_array($roles) ? $roles : func_get_args();
        $normalized = array_map(fn($r) => strtoupper((string) $r), $roles);

        // Alias penanganan USER_DOKTER dan DOKTER
        if (in_array('DOKTER', $normalized) && $userRole === 'USER_DOKTER') {
            return true;
        }

        return in_array($userRole, $normalized, true);
    }

    public function isSuperAdmin(): bool
    {
        return strtoupper((string) $this->role) === 'SUPER_ADMIN';
    }

    public function isAdminPusat(): bool
    {
        return strtoupper((string) $this->role) === 'ADMIN_PUSAT';
    }

    public function isAdminPogi(): bool
    {
        return strtoupper((string) $this->role) === 'ADMIN_POGI';
    }

    public function isAdminCabang(): bool
    {
        return strtoupper((string) $this->role) === 'ADMIN_CABANG';
    }

    public function isDoctor(): bool
    {
        $role = strtoupper((string) $this->role);
        return $role === 'USER_DOKTER' || $role === 'DOKTER';
    }

    /**
     * Cek apakah user berwenang mengakses cabang tertentu
     */
    public function canAccessBranch(?string $branchId): bool
    {
        if ($this->isSuperAdmin() || $this->isAdminPusat() || $this->isAdminPogi()) {
            return true; // Akses nasional
        }

        if ($this->isAdminCabang()) {
            return !empty($this->branch_id) && $this->branch_id === $branchId;
        }

        return false;
    }
}
