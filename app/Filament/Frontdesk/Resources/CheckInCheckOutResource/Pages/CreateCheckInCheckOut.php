<?php

namespace App\Filament\Frontdesk\Resources\CheckInCheckOutResource\Pages;

use App\Filament\Frontdesk\Resources\CheckInCheckOutResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use App\Models\Reservation;
use Filament\Notifications\Notification;

class CreateCheckInCheckOut extends CreateRecord
{
    protected static string $resource = CheckInCheckOutResource::class;
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Check if the status is 'Checked In'
        if ($data['status'] === 'Checked In') {
            // Find the selected reservation by 'reservation_id' in the form
            $reservation = Reservation::find($data['reservation_id']);

            if ($reservation) {
                // Update the reservation status to 'Checked In'
                $reservation->update(['status' => 'Checked In']);

                // Optionally, update the room status (set to unavailable)
                $reservation->room->update(['status' => 0]);
            } else {
                // Show a notification if the reservation was not found
                Notification::make()
                    ->title('Reservation not found!')
                    ->danger()
                    ->send();
            }
        }

        return $data; // Proceed with creating the CheckInCheckOut record
    }
}
