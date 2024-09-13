<?php
namespace App\Filament\Frontdesk\Pages;

use App\Filament\Frontdesk\Widgets\AvailableRooms;
use App\Filament\Frontdesk\Widgets\StatsOverview;
use App\Filament\Frontdesk\Widgets\LatestChecked;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public $activeCard = null;

    public function getWidgets(): array
    {
        $widgets = [
            StatsOverview::class,  // Always show the stats overview
        ];

        // Add the corresponding table widget for the active card if it exists
        if ($widget = $this->getTableWidgetForActiveCard()) {
            $widgets[] = $widget;
        }

        return $widgets;
    }

    protected function getTableWidgetForActiveCard(): ?string
    {
        switch ($this->activeCard) {
            case 'CheckedInGuests':
                return LatestChecked::class;
            case 'AvailableRooms':
                // Return the widget class for available rooms
                return AvailableRooms::class;
            case 'ActiveReservations':
                // Return the widget class for active reservations
                return AvailableRooms::class;
            default:
                return null;  // Return null if no active card
        }
    }
}
