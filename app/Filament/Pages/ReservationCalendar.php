<?php

namespace App\Filament\Pages;
use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class ReservationCalendar  extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    protected static string $routePath = 'Reservation Calendar';
    protected static ?string $title = 'Reservation Calendar';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    protected static ?int $navigationSort = -1;

  
    public function getWidgets(): array
    {
        return [
        
           CalendarWidget::class
        ];
    }

  
}
