<?php

namespace App\Providers\Filament;

use App\Filament\Management\Pages\OrderReport;
use App\Filament\Management\Pages\Printer;
use App\Filament\Management\Pages\RestaurantReport;
use App\Filament\Management\Resources\UserResource;
use App\Filament\Management\Resources\StaffManagementResource;
use App\Filament\Management\Resources\CouponManagementResource;
use App\Filament\Management\Resources\MenuCategoryResource;
use App\Filament\Management\Resources\MenuItemResource;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Http\Middleware\RoleRedirect;

use Illuminate\Support\Facades\Route;

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
            ->assets([
                Js::make('custom-javascript', resource_path('js/app.js')),
                Js::make('printer', resource_path('js/printer.js')),
            ])
            ->navigationGroups([
                'General Reports',
                'Management', 
                'Marketing',  
                'Menu Management'  ,
                'Configurations'       
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Front Desk')
                    ->url('/frontdesk')
                    ->icon('heroicon-o-users')
                    ->visible(fn(): bool => auth()->user()->role === 'Manager'),
                MenuItem::make()
                    ->label('Restaurant')
                    ->url('/restaurant')
                    ->icon('heroicon-o-squares-2x2')
                    ->visible(fn(): bool => auth()->user()->role === 'Manager')
            ])
            ->resources($this->getResources())
            ->pages($this->getPages())
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

    // Use only the required resources
    protected function getResources(): array
    {
        return [
            UserResource::class,
            StaffManagementResource::class,
            CouponManagementResource::class,
            MenuItemResource::class,
            MenuCategoryResource::class,
        ];
    }
    protected function getPages(): array
    {
        return [
            Pages\Dashboard::class,
            RestaurantReport::class,
            OrderReport::class, 
            Printer::class,
        ];
    }

  

}

