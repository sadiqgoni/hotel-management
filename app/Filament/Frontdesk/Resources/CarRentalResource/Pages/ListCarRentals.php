<?php

namespace App\Filament\Frontdesk\Resources\CarRentalResource\Pages;

use App\Filament\Frontdesk\Resources\CarRentalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarRentals extends ListRecords
{
    protected static string $resource = CarRentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
