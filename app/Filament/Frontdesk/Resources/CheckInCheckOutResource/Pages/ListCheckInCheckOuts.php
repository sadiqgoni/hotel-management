<?php

namespace App\Filament\Frontdesk\Resources\CheckInCheckOutResource\Pages;

use App\Filament\Frontdesk\Resources\CheckInCheckOutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCheckInCheckOuts extends ListRecords
{
    protected static string $resource = CheckInCheckOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
