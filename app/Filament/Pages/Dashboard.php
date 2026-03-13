<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminStats;
use App\Filament\Widgets\BatchChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $navigationLabel = 'Dashboard'; 
    
    protected static ?string $title = 'Dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            AdminStats::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            BatchChart::class,
        ];
    }
}