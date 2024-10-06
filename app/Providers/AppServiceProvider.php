<?php

namespace App\Providers;

use App\Models\CheckIn;
use App\Observers\CheckInObserver;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        CheckIn::observe(CheckInObserver::class);


        FilamentColor::register([
            'burgundy' => Color::hex('#800020'), // Example hex code for Burgundy
            'cream' => Color::hex('#FFFDD0'), // Example hex code for Cream
            'gold' => Color::hex('#FFD700'), // Example hex code for Gold
            'navy-blue' => Color::hex('#000080'), // Navy Blue
            'soft-gray' => Color::hex('#D3D3D3'), // Soft Gray
            'coral' => Color::hex('#FF7F50'), // Coral
            'forest-green' => Color::hex('#228B22'), // Forest Green
            'soft-beige' => Color::hex('#F5F5DC'), // Soft Beige
            'warm-orange' => Color::hex('#FF8C00'), // Warm Orange
        ]);

    }
}
