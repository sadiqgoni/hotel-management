<?php

namespace App\Filament\Frontdesk\Pages;
use App\Filament\Frontdesk\Widgets\CalendarWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class ReservationCalendar  extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    protected static string $routePath = 'Reservation Calendar';
    protected static ?string $title = 'Reservation Calendar';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $navigationGroup = 'Operations Management';

    protected static ?int $navigationSort = 1;

  
    public function getWidgets(): array
    {
        return [
        
           CalendarWidget::class
        ];
    }

  
}
