<?php

namespace App\Filament\Frontdesk\Resources\CheckInCheckOutResource\Pages;

use App\Filament\Frontdesk\Resources\CheckInCheckOutResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\Reservation;
use Filament\Actions;
use Filament\Notifications\Notification;
class EditCheckInCheckOut extends EditRecord
{
    protected static string $resource = CheckInCheckOutResource::class;
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Check if the status has been changed to 'Checked In'
        if ($data['status'] === 'Checked In') {
            // Find the reservation by 'reservation_id'
            $reservation = Reservation::find($data['reservation_id']);

            if ($reservation) {
                // Update the reservation status to 'Checked In'
                $reservation->update(['status' => 'Checked In']);

                // Optionally, mark the room as unavailable
                $reservation->room->update(['status' => 0]);
            } else {
                Notification::make()
                    ->title('Reservation not found!')
                    ->danger()
                    ->send();
            }
        }

        // Check if the status has been changed to 'Checked Out'
        if ($data['status'] === 'Checked Out') {
            // Find the reservation by 'reservation_id'
            $reservation = Reservation::find($data['reservation_id']);

            if ($reservation) {
                // Update the reservation status to 'Checked Out'
                $reservation->update(['status' => 'Checked Out']);

                // Optionally, mark the room as available again
                $reservation->room->update(['status' => 1]);
            } else {
                Notification::make()
                    ->title('Reservation not found!')
                    ->danger()
                    ->send();
            }
        }

        return $data; // Proceed with saving the CheckInCheckOut record
    }
}