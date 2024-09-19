<?php

namespace App\Filament\Frontdesk\Resources\GroupReservationResource\Pages;

use App\Filament\Frontdesk\Resources\GroupReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGroupReservations extends ListRecords
{
    protected static string $resource = GroupReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
