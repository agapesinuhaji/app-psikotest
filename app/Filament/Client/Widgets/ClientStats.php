<?php

namespace App\Filament\Client\Widgets;

use App\Models\Batch;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ClientStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make( 'Total Batch', Batch::where('user_id', Auth::id())->count() ) ->description('Jumlah batch yang Anda miliki') ->color('primary'),

            Stat::make( 'Batch Aktif', Batch::where('user_id', Auth::id()) ->whereNotIn('status', ['closed', 'standby']) ->count() ) ->description('Batch yang sedang berjalan') ->color('success'),

            Stat::make( 'Menunggu Pembayaran', Batch::where('user_id', Auth::id()) ->whereHas('payment', function ($q) { $q->where('status', 'pending'); }) ->count() ) ->description('Batch yang belum dibayar') ->color('warning'),
        ];
    }
}
