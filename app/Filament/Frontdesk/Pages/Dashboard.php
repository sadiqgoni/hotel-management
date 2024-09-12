<?php

namespace App\Filament\Frontdesk\Pages;

use App\Filament\Frontdesk\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static ?int $navigationSort = -2;

    protected static string $routePath = 'Dashboard';
    protected static ?string $title = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            FilamentInfoWidget::class,
            StatsOverview::class
        ];
    }






}
