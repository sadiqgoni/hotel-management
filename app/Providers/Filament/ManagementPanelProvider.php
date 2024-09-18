<?php

namespace App\Providers\Filament;

use App\Filament\Management\Resources\StaffManagementResource;
use App\Filament\Management\Resources\UserResource;
use App\Filament\Management\Resources\CouponManagementResource;
use App\Http\Middleware\RoleRedirect;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
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

class ManagementPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('management')
            ->path('management')
            ->login()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Front Desk')
                    ->url('/frontdesk')
                    ->icon('heroicon-o-users')
                    ->visible(fn(): bool => auth()->user()->role === 'Manager')
            ])
            ->resources($this->getResources())
            ->pages($this->getPages())
            ->discoverWidgets(in: app_path('Filament/Management/Widgets'), for: 'App\\Filament\\Management\\Widgets')
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
            UserResource::class,
            StaffManagementResource::class,
            CouponManagementResource::class
        ];
    }
    protected function getPages(): array
    {
        return [
            Pages\Dashboard::class,
        ];
    }
}
