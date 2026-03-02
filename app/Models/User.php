<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Carbon\Carbon;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'nik',
        'nama_ayah',
        'username',
        'plain_password',
        'password',
        'address',
        'batch_id',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'phone',
        'photo',
        'last_education',
        'age',
        'is_admin',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth'     => 'date',
        ];
    }

    /**
     * 🛡️ PENGATURAN AKSES DASHBOARD BERDASARKAN ROLE
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            // Pintu 1: Khusus Administrator
            'admin'    => $this->role === 'administrator' || $this->is_admin == 1,
            
            // Pintu 2: Khusus Client/Pasien
            'client'   => $this->role === 'client',
            
            // Pintu 3: Khusus Psikolog
            'psikolog' => $this->role === 'psikolog',
            
            // Pintu Lainnya: Tertutup rapat
            default    => false,
        };
    }

    /**
     * 🔥 AUTO GENERATE SAAT CREATE USER
     */
    protected static function booted()
    {
        static::creating(function ($user) {

            // 🎯 hanya untuk participant
            if ($user->role !== 'participant') {
                return;
            }

            // ❗ NIK wajib
            if (empty($user->nik)) {
                throw new \Exception('NIK wajib diisi untuk participant');
            }

            $user->nik = (string) $user->nik;

            // 🔢 Hitung umur otomatis
            $user->age = !empty($user->date_of_birth)
                ? Carbon::parse($user->date_of_birth)->age
                : null;

            $user->username = $user->nik;

            if (empty($user->email)) {
                $user->email = $user->nik . '@example.com';
            }

            $plainPassword = $user->nik;
            $user->plain_password = $plainPassword;
            $user->password = bcrypt($plainPassword);

            $user->role = 'participant';
        });
    }

    /**
     * HELPER CHECKERS
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isPsikolog(): bool
    {
        return $this->role === 'psikolog';
    }

    public function isAdministrator(): bool
    {
        return $this->role === 'administrator' || $this->is_admin == 1;
    }
}