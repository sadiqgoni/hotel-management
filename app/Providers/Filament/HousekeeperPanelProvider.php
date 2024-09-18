<?php

namespace App\Providers\Filament;

use App\Filament\Frontdesk\Resources\RoomResource;
use App\Filament\Housekeeper\Resources\MaintenanceRequestResource;
use App\Http\Middleware\RoleRedirect;
use App\Models\MaintenanceRequest;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class HousekeeperPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('housekeeper')
            ->path('housekeeper')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->login()

            ->resources($this->getResources())
            ->pages($this->getPages())
        
            ->discoverWidgets(in: app_path('Filament/Housekeeper/Widgets'), for: 'App\\Filament\\Housekeeper\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
            ->authMiddleware([
                Authenticate::class,
                RoleRedirect::class,

            ]);
    }
    protected function getResources(): array
    {
        return [
            RoomResource::class,
            MaintenanceRequestResource::class

        ];
    }
    protected function getPages(): array
    {
        return [
            Pages\Dashboard::class,
        ];
    }
}
