<?php

namespace App\Providers\Filament;

use App\Filament\Pages\ReservationCalendar;
use Filament\Navigation\MenuItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;

use Filament\Support\Colors\Color;
use App\Filament\Pages\EditProfile;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->plugins([
                FilamentFullCalendarPlugin::make()
                ->selectable()
                ->editable()
            ])
            ->unsavedChangesAlerts()
            ->colors([
                'primary' => Color::hex('#166534'),
            ])
            ->navigationGroups([
                'Front Desk',            
            ])
            ->globalSearch(false)
            ->globalSearchKeyBindings(['command+', 'ctrl+k'])
            
            ->brandLogo(asset('images/hotel3.jpg'))
            ->darkModeBrandLogo(asset('images/hotel2.png'))
            ->favicon(asset('images/hotel3.jpg'))
            ->brandLogoHeight('3.5rem')

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                ReservationCalendar::class
            ])
            ->userMenuItems([
                MenuItem::make()
                ->label('Management')
                ->url('/management')
                ->icon('heroicon-o-squares-2x2'),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
         
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
            ])
            ->plugins([
                FilamentBackgroundsPlugin::make()
                ->remember(200)
                ->imageProvider(
                    MyImages::make()
                    ->directory('assets/background')
                    )
                ->showAttribution(false),


                // FilamentApexChartsPlugin::make()
             ]);
    }
}
