<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'plain_password',
        'password',
        'batch_id',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'last_education',
        'age',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Hanya izinkan jika pengguna adalah admin
        return $this->is_admin;
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

            $username = "{$date}{$batchId}{$microSegment}{$random}";
            $user->username = $username;

            // 3. Email otomatis (jika kosong)
            if (empty($user->email)) {
                $user->email = $username . '@example.com';
            }

            // ------------------------------------------------------
            // 4. PASSWORD BERDASARKAN ROLE
            // ------------------------------------------------------

            // Jika USER biasa (bukan admin)
            if ($user->is_admin != 1) {

                // generate password otomatis
                $plainPassword = self::generateRandomPassword(8);

                // simpan plain password
                $user->plain_password = $plainPassword;

                // hash untuk database
                $user->password = bcrypt($plainPassword);

            } 
            // Jika ADMIN → password dari input, tidak diubah
            else {

                // pastikan password sudah ada dan tinggal hash
                if (!empty($user->password)) {
                    // generate password otomatis
                    $plainPassword = self::generateRandomPassword(8);

                    // simpan plain password
                    $user->plain_password = $plainPassword;
                    $user->password = bcrypt($user->password);
                }
            }
        });
    }


    /**
     * Generator password acak
     */
    private static function generateRandomPassword($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $password;
    }

}
