<?php

namespace App\Providers\Filament;

use App\Filament\Restaurant\Pages\RestaurantMenu;
use App\Filament\Management\Resources\UserResource;
use App\Filament\Restaurant\Resources\OrderResource;
use App\Http\Middleware\RoleRedirect;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Navigation\MenuItem;
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

class RestaurantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('restaurant')
            ->path('restaurant')
            ->login()
            ->colors([
                'primary' => Color::hex('#FF7F50')
            ])
            ->resources($this->getResources())
            ->pages($this->getPages())
            ->userMenuItems([
                MenuItem::make()
                    ->label('Front Desk')
                    ->url('/frontdesk')
                    ->icon('heroicon-o-users')
                    ->visible(condition: fn(): bool => auth()->user()->role === 'Manager'),
                MenuItem::make()
                    ->label('Management')
                    ->url('/management')
                    ->icon('heroicon-o-squares-2x2')
                    ->visible(fn(): bool => auth()->user()->role === 'Manager')
            ])
            ->discoverWidgets(in: app_path('Filament/Restaurant/Widgets'), for: 'App\\Filament\\Restaurant\\Widgets')
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
            OrderResource::class
            
        ];
    }
    protected function getPages(): array
    {
        return [
            Pages\Dashboard::class,
            RestaurantMenu::class


        ];
    }
}
