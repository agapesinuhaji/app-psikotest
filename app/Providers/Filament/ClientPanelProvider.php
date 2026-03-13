<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;

use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;

use Illuminate\Support\Facades\Route;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use App\Http\Controllers\ClientBatchTemplateController;

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('client')
            ->path('client')

            ->colors([
                'primary' => Color::Amber,
            ])

            /*
            |--------------------------------------------------------------------------
            | Resources, Pages & Widgets
            |--------------------------------------------------------------------------
            */

            ->discoverResources(
                in: app_path('Filament/Client/Resources'),
                for: 'App\\Filament\\Client\\Resources'
            )

            ->discoverPages(
                in: app_path('Filament/Client/Pages'),
                for: 'App\\Filament\\Client\\Pages'
            )

            ->pages([
                Dashboard::class,
            ])

            ->discoverWidgets(
                in: app_path('Filament/Client/Widgets'),
                for: 'App\\Filament\\Client\\Widgets'
            )

            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
            ])

            /*
            |--------------------------------------------------------------------------
            | 🔥 Custom Routes Inside Client Panel
            |--------------------------------------------------------------------------
            */

            ->routes(function () {
                Route::get(
                    '/batches/download-template',
                    [ClientBatchTemplateController::class, 'download']
                )->name('client.batches.download-template');
            })

            /*
            |--------------------------------------------------------------------------
            | Middleware Stack
            |--------------------------------------------------------------------------
            */

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            /*
            |--------------------------------------------------------------------------
            | Authentication Middleware (Panel Auth)
            |--------------------------------------------------------------------------
            */

            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}