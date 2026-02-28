<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Filament\Notifications\Notification as FilamentNotification;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantsImport implements ToCollection, WithHeadingRow
{
    protected $batchId;

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }

    public function collection(Collection $rows)
    {
        $duplicates = [];
        $inserted = 0;

        foreach ($rows as $row) {

            // 🔥 normalisasi key (hindari spasi / kapital)
            $row = collect($row)->mapWithKeys(fn ($v, $k) => [Str::lower(trim($k)) => $v])->toArray();

            $name  = $row['name'] ?? null;
            $email = strtolower(trim($row['email'] ?? ''));

            $nik          = isset($row['nik']) ? (string) $row['nik'] : null;
            $namaAyah     = $row['nama_ayah'] ?? null;
            $placeOfBirth = $row['place_of_birth'] ?? null;
            $gender       = $row['gender'] ?? null;
            $education    = $row['last_education'] ?? null;

            // 🔥 format tanggal
            $dateOfBirth = null;
            if (!empty($row['date_of_birth'])) {
                try {
                    $dateOfBirth = Carbon::parse($row['date_of_birth'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $dateOfBirth = null;
                }
            }

            // 🔥 format phone
            $phone = $this->formatPhone($row['phone'] ?? '');

            // skip kalau tidak ada email & phone
            if (!$email && !$phone) {
                continue;
            }

            // 🔍 cek duplicate dalam batch
            $exists = User::where('batch_id', $this->batchId)
                ->where(function ($q) use ($email, $phone) {
                    if ($email) {
                        $q->orWhere('email', $email);
                    }
                    if ($phone) {
                        $q->orWhere('phone', $phone);
                    }
                })
                ->exists();

            if ($exists) {
                $duplicates[] = $email ?: $phone;
                continue;
            }

            // 💾 simpan ke database
            User::create([
                'batch_id'        => $this->batchId,
                'name'            => $name,
                'email'           => $email,
                'nik'             => $nik,
                'nama_ayah'       => $namaAyah,
                'place_of_birth'  => $placeOfBirth,
                'date_of_birth'   => $dateOfBirth,
                'gender'          => $gender,
                'last_education'  => $education,
                'phone'           => $phone,
                'role'            => 'participant',
                'is_active'       => 0,
            ]);

            $inserted++;
        }

        // ✅ notif sukses
        FilamentNotification::make()
            ->title("Import selesai")
            ->body("$inserted peserta berhasil ditambahkan")
            ->success()
            ->send();

        // ⚠️ notif duplicate
        if (count($duplicates) > 0) {
            FilamentNotification::make()
                ->title("Data duplikat ditemukan")
                ->body("Peserta berikut sudah terdaftar:\n" . implode(', ', $duplicates))
                ->danger()
                ->send();
        }
    }

    /**
     * 🔥 FORMAT NOMOR HP
     */
    private function formatPhone($phone)
    {
        $phone = preg_replace('/\D/', '', (string) $phone);

        if (substr($phone, 0, 2) === '62') {
            $phone = '0' . substr($phone, 2);
        }

        if ($phone && substr($phone, 0, 1) !== '0') {
            $phone = '0' . $phone;
        }

        return $phone;
    }
}