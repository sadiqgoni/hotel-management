<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRoom extends CreateRecord
{
    protected static string $resource = RoomResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     if (isset($data['status']) && $data['status']) {
    //         $data['status'] = 'available';
    //     } else {
    //         $data['status'] = 'unavailable';
    //     }

    //     return $data;
    // }
}
