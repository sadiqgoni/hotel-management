<?php

namespace App\Filament\Frontdesk\Resources\CarResource\Pages;

use App\Filament\Frontdesk\Resources\CarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCar extends EditRecord
{
    protected static string $resource = CarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
