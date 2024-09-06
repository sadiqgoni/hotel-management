<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoom extends EditRecord
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     // Assuming you are checking some condition for status
    //     if (isset($data['status']) && $data['status']) {
    //         $data['status'] = 'available';
    //     } else {
    //         $data['status'] = 'unavailable';
    //     }

    //     return $data;
    // }
}
