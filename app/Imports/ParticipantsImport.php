<?php

namespace App\Imports;

use App\Models\Participant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Maatwebsite\Excel\Concerns\ToCollection;

class ParticipantsImport implements ToCollection
{
    protected $batchId;

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }

    public function collection(Collection $rows)
    {
        // hapus header
        $rows->shift();

        $duplicates = [];
        $inserted = 0;

        foreach ($rows as $row) {
            $name  = $row[0] ?? null;
            $email = strtolower(trim($row[1] ?? ''));
            $phone = $this->formatPhone($row[2] ?? '');

            if (!$email && !$phone) {
                continue;
            }

            // cek duplicate di batch yang sama
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

            User::create([
                'batch_id' => $this->batchId,
                'name'     => $name,
                'email'    => $email,
                'phone'    => $phone,
            ]);

            $inserted++;
        }

        // notif sukses
        FilamentNotification::make()
            ->title("Import selesai")
            ->body("$inserted peserta berhasil ditambahkan")
            ->success()
            ->send();

        // notif duplicate
        if (count($duplicates) > 0) {
            FilamentNotification::make()
                ->title("Data duplikat ditemukan")
                ->body("Peserta berikut sudah terdaftar di batch ini:\n" . implode(', ', $duplicates))
                ->danger()
                ->send();
        }
    }

    /**
     * 🔥 FIX NOMOR HP DARI EXCEL
     */
    private function formatPhone($phone)
    {
        $phone = preg_replace('/\D/', '', (string) $phone);

        // kalau diawali 62 -> ubah ke 0
        if (substr($phone, 0, 2) === '62') {
            $phone = '0' . substr($phone, 2);
        }

        // kalau tidak diawali 0 -> tambahkan 0
        if ($phone && substr($phone, 0, 1) !== '0') {
            $phone = '0' . $phone;
        }

        return $phone;
    }
}