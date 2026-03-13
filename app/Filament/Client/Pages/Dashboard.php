<?php

namespace App\Filament\Client\Pages;

use App\Filament\Client\Widgets\ClientStats;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $navigationLabel = 'Dashboard'; 
    
    protected static ?string $title = 'Dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            ClientStats::class,
        ];
    }
}
