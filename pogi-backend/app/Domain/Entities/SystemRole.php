<?php

namespace App\Domain\Entities;

enum SystemRole: string
{
    case SUPER_ADMIN  = 'SUPER_ADMIN';
    case ADMIN_PUSAT  = 'ADMIN_PUSAT';
    case ADMIN_POGI   = 'ADMIN_POGI';
    case ADMIN_CABANG = 'ADMIN_CABANG';
    case DOKTER       = 'DOKTER';
    case USER_DOKTER  = 'USER_DOKTER';

    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN  => 'Super Admin (PT Energi Cipta)',
            self::ADMIN_PUSAT  => 'Pengurus Pusat POGI (Monitoring & SLA)',
            self::ADMIN_POGI   => 'Admin POGI (Verifikator SKP)',
            self::ADMIN_CABANG => 'Pengurus Cabang Daerah',
            self::DOKTER, self::USER_DOKTER => 'Dokter Spesialis Obgyn (Anggota)',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::SUPER_ADMIN  => 'purple',
            self::ADMIN_PUSAT  => 'indigo',
            self::ADMIN_POGI   => 'emerald',
            self::ADMIN_CABANG => 'sky',
            self::DOKTER, self::USER_DOKTER => 'teal',
        };
    }
}
