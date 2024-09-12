<?php

namespace App\Filament\Frontdesk\Resources\RoomTypeResource\Pages;

use App\Filament\Frontdesk\Resources\RoomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomTypes extends ListRecords
{
    protected static string $resource = RoomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
