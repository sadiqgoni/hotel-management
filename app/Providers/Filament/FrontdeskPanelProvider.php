<?php

namespace App\Providers\Filament;

use App\Filament\Frontdesk\Pages\CheckOutPage;
use App\Filament\Frontdesk\Pages\Dashboard;
use App\Filament\Frontdesk\Pages\ReservationCalendar;
use App\Filament\Frontdesk\Resources\CarRentalResource;
use App\Filament\Frontdesk\Resources\CarResource;
use App\Filament\Frontdesk\Resources\CheckInCheckOutResource;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Filament\Frontdesk\Resources\CheckInResource;
use App\Filament\Frontdesk\Resources\GroupReservationResource;
use App\Filament\Frontdesk\Resources\GuestResource;
use App\Filament\Frontdesk\Resources\ReservationResource;
use App\Filament\Frontdesk\Resources\ReservationWaitlistResource;
use App\Filament\Frontdesk\Resources\RoomResource;
use App\Filament\Frontdesk\Resources\RoomTypeResource;
use App\Filament\Housekeeper\Resources\MaintenanceRequestResource;
use App\Filament\Frontdesk\Pages\Sample;
use App\Http\Middleware\RoleRedirect;
use App\Models\MaintenanceRequest;
use App\Models\RoomType;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Support\Colors\Color;
use Filament\Navigation\MenuItem;
use App\Filament\Pages\EditProfile;
use Filament\Navigation\NavigationGroup;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
class FrontdeskPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        // Register scroll to top event
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::GLOBAL_SEARCH_AFTER,
        //     fn(): string => Blade::render("@livewire('report.day-close')"),
        // );
    }
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('frontdesk')
            ->path('frontdesk')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->login()
            ->colors([
                'primary' => Color::hex('#166534'),
            ])
            ->navigationGroups([
                'Daily Operations',
                'Operations Management',
                'Rooms Management',
                'Guest Management',
                'Transport Management',
                'Group Management',
            ])
            ->globalSearch(false)
            ->globalSearchKeyBindings(['command+', 'ctrl+k'])

            ->brandLogo(asset('images/hotel3.jpg'))
            ->darkModeBrandLogo(asset('images/hotel2.png'))
            ->favicon(asset('images/hotel3.jpg'))
            ->brandLogoHeight('3.5rem')
            ->resources($this->getResources())
            ->pages($this->getPages())

            ->userMenuItems([
                MenuItem::make()
                    ->label('Management')
                    ->url('/management')
                    ->icon('heroicon-o-squares-2x2')
                    ->visible(fn(): bool => auth()->user()->role === 'Manager'),
                MenuItem::make()
                    ->label(label: 'Restaurant')
                    ->url('/restaurant')
                    ->icon('heroicon-o-squares-2x2')
                    ->visible(fn(): bool => auth()->user()->role === 'Manager')
            ])
            ->discoverWidgets(in: app_path('Filament/Frontdesk/Widgets'), for: 'App\\Filament\\Frontdesk\\Widgets')

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
                RoleRedirect::class
            ])
            ->plugins([
                FilamentBackgroundsPlugin::make()
                    ->remember(200)
                    ->imageProvider(
                        MyImages::make()
                            ->directory('assets/background')
                    )
                    ->showAttribution(false),

                FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable(),



                // FilamentApexChartsPlugin::make()
            ]);

    }

    protected function getResources(): array
    {
        return [
            CheckInResource::class,
            GuestResource::class,
            ReservationResource::class,
            RoomResource::class,
            RoomTypeResource::class,
            MaintenanceRequestResource::class,
            GroupReservationResource::class,
            ReservationWaitlistResource::class,
            CarRentalResource::class,
            CarResource::class,

        ];
    }
    protected function getPages(): array
    {
        return [
            Dashboard::class,
            ReservationCalendar::class,
            CheckOutPage::class,


        ];
    }
}
