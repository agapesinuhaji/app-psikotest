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

            // 🔥 normalisasi key header (hindari spasi / kapital)
            $row = collect($row)
                ->mapWithKeys(fn ($v, $k) => [Str::lower(trim($k)) => $v])
                ->toArray();

            $name  = $row['name'] ?? null;
            $email = strtolower(trim($row['email'] ?? ''));

            $nik          = isset($row['nik']) ? (string) $row['nik'] : null;
            $namaAyah     = $row['nama_ayah'] ?? null;
            $placeOfBirth = $row['place_of_birth'] ?? null;
            $gender       = $row['gender'] ?? null;
            $education    = $row['last_education'] ?? null;

            /**
             * 🔥 format tanggal lahir
             */
            $dateOfBirth = null;

            if (!empty($row['date_of_birth'])) {
                try {
                    $dateOfBirth = Carbon::parse($row['date_of_birth'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $dateOfBirth = null;
                }
            }

            /**
             * 🔥 format nomor HP
             */
            $phone = $this->formatPhone($row['phone'] ?? '');

            // skip kalau email dan phone kosong
            if (!$email && !$phone) {
                continue;
            }

            /**
             * 🔍 cek duplikasi dalam batch yang sama
             */
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

            /**
             * 💾 simpan peserta
             */
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

        /**
         * ✅ NOTIFIKASI IMPORT BERHASIL
         */
        FilamentNotification::make()
            ->title("Import selesai")
            ->body("$inserted peserta berhasil ditambahkan")
            ->success()
            ->send();

        /**
         * ⚠️ NOTIFIKASI DUPLIKAT
         */
        if (count($duplicates) > 0) {

            $duplicateList = array_slice($duplicates, 0, 5); // tampilkan max 5
            $more = count($duplicates) > 5 ? ' dan lainnya...' : '';

            FilamentNotification::make()
                ->title("Data duplikat ditemukan")
                ->body(
                    "Peserta berikut sudah terdaftar:\n" .
                    implode(', ', $duplicateList) .
                    $more
                )
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

        if (!$phone) {
            return null;
        }

        // ubah 62 menjadi 0
        if (substr($phone, 0, 2) === '62') {
            $phone = '0' . substr($phone, 2);
        }

        // jika tidak diawali 0
        if (substr($phone, 0, 1) !== '0') {
            $phone = '0' . $phone;
        }

        return $phone;
    }
}