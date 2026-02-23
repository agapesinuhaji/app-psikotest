<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

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
        ];
    }

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

    protected static function booted()
    {
        static::creating(function ($user) {

            // 1. Hitung umur
            if (!empty($user->date_of_birth)) {
                $user->age = \Carbon\Carbon::parse($user->date_of_birth)->age;
            }

            // 2. Generate Username Otomatis
            $batchId = $user->batch_id ?? 0;
            $date = now()->format('md'); 
            $micro = microtime(true);
            $microDigits = preg_replace('/\D/', '', $micro);
            $microSegment = substr($microDigits, -4);
            $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            $user->username = "{$date}{$batchId}{$microSegment}{$random}";

            // 3. Email otomatis
            if (empty($user->email)) {
                $user->email = $user->username . '@example.com';
            }

            // 🔥 4. PAKSA ROLE PARTICIPANT (jika bukan admin)
            if ($user->is_admin != 1) {
                $user->role = $user->role ?? 'participant';
            }

            // 5. Password
            if ($user->is_admin != 1) {
                $plainPassword = self::generateRandomPassword(8);
                $user->plain_password = $plainPassword;
                $user->password = bcrypt($plainPassword);
            } else {
                if (!empty($user->password)) {
                    $plainPassword = self::generateRandomPassword(8);
                    $user->plain_password = $plainPassword;
                    $user->password = bcrypt($user->password);
                }
            }
        });
    }

    private static function generateRandomPassword($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $password;
    }

    public function isClient()
    {
        return $this->role === 'client';
    }
}