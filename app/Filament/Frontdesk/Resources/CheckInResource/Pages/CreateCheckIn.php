<?php

namespace App\Filament\Frontdesk\Resources\CheckInResource\Pages;

use App\Filament\Frontdesk\Resources\CheckInResource;
use App\Models\Reservation;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateCheckIn extends CreateRecord
{
    protected static string $resource = CheckInResource::class;

    // Redirect user back to the check-in list after creation
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Mutate form data before creating the check-in record
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the currently authenticated user as the creator of the check-in
        $data['user_id'] = auth()->id();

        return $data;
    }

    // Customize the notification after check-in creation
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Check-in Created')
            ->body('The check-in has been successfully created!');
    }

    protected function afterCreate(): void
    {
        DB::transaction(function () {



            try {
                // Fetch the reservation
                $reservation = Reservation::find($this->data['reservation_id']);

                // Check if the reservation exists before proceeding
                if ($reservation) {
                    // Update the check-in model with reservation data
                    $this->record->update([
                        'guest_name' => $reservation->guest->name,
                        'guest_phone' => $reservation->guest->phone_number,
                        'room_number' => $reservation->room->number,
                        'paid_amount' => $reservation->amount_paid,
                        'due_amount' => $reservation->total_amount - $reservation->amount_paid,
                        'booking_status' => $reservation->status,
                        'payment_status' => $reservation->payment_status,
                        'coupon_management' => $reservation->coupon_management_id,
                        'coupon_discount' => $reservation->coupon_discount,
                        'price_per_night' => $reservation->price_per_night,
                        'frequent_guest_message' => $reservation->frequent_guest_message,
                        'number_of_nights' => $reservation->number_of_nights,
                        'special_requests' => $reservation->special_requests,
                        'number_of_people' => $reservation->number_of_people,
                        'total_amount' => $reservation->total_amount,
                    ]);
                }
                $reservation->delete();

                // Once the check-in is created, delete the reservation

            } catch (\Exception $e) {
                Log::error('Check-in save failed: ' . $e->getMessage());
                throw $e; // rethrow to rollback transaction
            }

        });
    }


}
