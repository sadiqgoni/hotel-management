<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // Check if status is 'Confirmed' or 'Checked In'
    //     if ($data['status'] === 'Confirmed' || $data['status'] === 'Checked In') {
    //         $room = \App\Models\Room::find($data['room_id']);
    //         // Set room status to '0' (unavailable)
    //         if ($room) {
    //             $room->update(['status' => '0']);
    //         }
    //     }

    //     // Check if status is 'Checked Out'
    //     if ($data['status'] === 'Checked Out') {
    //         $room = \App\Models\Room::find($data['room_id']);
    //         // Set room status to '1' (available)
    //         if ($room) {
    //             $room->update(['status' => '1']);
    //         }
    //     }

    //     return $data;
    // }
}
