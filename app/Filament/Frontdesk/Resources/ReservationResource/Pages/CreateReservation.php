<?php

namespace App\Filament\Frontdesk\Resources\ReservationResource\Pages;

use App\Filament\Frontdesk\Resources\ReservationResource;
use App\Models\CouponManagement;
use App\Models\Guest;
use App\Models\Room;
use Filament\Actions;
use Illuminate\Support\Facades\Log;

use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Fetch the coupon ID from the data
        $couponId = $data['coupon_management_id'] ?? null;

        if ($couponId) {
            // Find the coupon
            $coupon = CouponManagement::find($couponId);

            if ($coupon) {
                // Update the times_used count
                $coupon->increment('times_used');

                // Check if the usage limit is reached
                if ($coupon->times_used >= $coupon->usage_limit) {
                    // Deactivate the coupon
                    $coupon->update(['status' => 'inactive']);
                }
            }
        }

        $guestId = $data['guest_id'] ?? null;

        if ($guestId) {
            $guest = Guest::find($guestId);
        
            if ($guest) {

                $guest->increment('stay_count'); // Increment the stay count
                $guest->save(); // Ensure the guest record is saved after incrementing
            } 
        }
        

        // Perform any other actions needed after saving the reservation
        $roomId = $data['room_id'];

        // Mark the room as unavailable after reservation is confirmed
        if ($roomId) {
            $room = Room::find($roomId);
            if ($room) {
                $room->update(['status' => false]); // Mark room as unavailable
            }
        }
        return $data;
    }





}
