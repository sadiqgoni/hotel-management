<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Support\Colors\Color;

use Filament\Resources\Pages\ListRecords;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->query(fn($query) => $query)
                ->icon('heroicon-o-bars-4'),

            'Confirmed' => Tab::make('Confirmed')
                ->query(fn($query) => $query->where('status', 'Confirmed')),
            'On Hold' => Tab::make('On Hold')
                ->query(fn($query) => $query->where('status', 'On Hold')),

            'Checked In' => Tab::make('Checked In')
                ->query(fn($query) => $query->where('status', 'Checked In'))
                ->badgeColor('danger'),
            'Checked Out' => Tab::make('Checked Out')
                ->query(fn($query) => $query->where('status', 'Checked Out'))
                ->badgeColor('gray'),
            'Cancelled' => Tab::make('Cancelled')
                ->query(fn($query) => $query->where('status', 'Cancelled'))
                ->badgeColor('red')
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return ReservationResource::getWidgets();
    }
}
