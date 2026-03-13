<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Batch;

class AdminStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make('Total Batch', Batch::count()) ->description('Jumlah seluruh batch') ->color('primary'),

            Stat::make( 'Batch Aktif', Batch::whereNotIn('status', ['closed', 'standby'])->count() ) ->description('Batch yang sedang berjalan') ->color('success'),

            Stat::make( 'Batch Paid', Batch::whereHas('payment', function ($q) { $q->where('status', 'paid'); })->count() ) ->description('Batch yang sudah dibayar') ->color('warning'),
        ];
    }
}