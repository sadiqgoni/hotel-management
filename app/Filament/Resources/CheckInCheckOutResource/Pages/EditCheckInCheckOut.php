<?php

namespace App\Filament\Resources\CheckInCheckOutResource\Pages;

use App\Filament\Resources\CheckInCheckOutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCheckInCheckOut extends EditRecord
{
    protected static string $resource = CheckInCheckOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
