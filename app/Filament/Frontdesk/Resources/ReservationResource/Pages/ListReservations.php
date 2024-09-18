<?php

namespace App\Filament\Frontdesk\Resources\ReservationResource\Pages;

use App\Filament\Frontdesk\Resources\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;

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