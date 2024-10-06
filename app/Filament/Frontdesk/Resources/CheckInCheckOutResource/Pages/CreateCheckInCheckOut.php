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

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }


    // After the CheckInCheckOut record is created, delete the reservation
    protected function afterCreate(): void
    {
        // Find the reservation by the 'reservation_id' provided in the form
        $reservation = Reservation::find($this->record->reservation_id);

        if ($reservation) {
            // Delete the reservation after check-in is created
            $reservation->delete();

            // Optionally, update the room status (set to unavailable)
            $reservation->room->update(['status' => 0]);

            // Show a success notification
            Notification::make()
                ->title('Guest checked in!')
                ->success()
                ->send();
        } else {
            // Show an error notification if the reservation wasn't found
            Notification::make()
                ->title('Reservation not found!')
                ->danger()
                ->send();
        }
    }
}
