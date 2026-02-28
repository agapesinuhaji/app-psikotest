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
     * Hak akses panel Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->is_admin == 1;
        }

        if ($panel->getId() === 'client') {
            return $this->role === 'client';
        }

        return false;
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

            // ❗ pastikan NIK string (agar 0 di depan tidak hilang)
            $user->nik = (string) $user->nik;

            // 🔢 Hitung umur otomatis
            $user->age = !empty($user->date_of_birth)
                ? Carbon::parse($user->date_of_birth)->age
                : null;

            // 👤 USERNAME = NIK
            $user->username = $user->nik;

            // 📧 Email otomatis jika kosong
            if (empty($user->email)) {
                $user->email = $user->nik . '@example.com';
            }

            // 🔐 PASSWORD = NIK
            $plainPassword = $user->nik;

            $user->plain_password = $plainPassword;
            $user->password = bcrypt($plainPassword);

            // 🔒 force role participant
            $user->role = 'participant';
        });
    }

    /**
     * Helper cek role client
     */
    public function isClient()
    {
        return $this->role === 'client';
    }
}