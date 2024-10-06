<?php

namespace App\Observers;

use App\Models\CheckIn;
use App\Models\Reservation;

class CheckInObserver
{
    /**
     * Handle the CheckIn "created" event.
     */
    // public function created(CheckIn $checkIn)
    // {
    //     // Find the related reservation and delete it
    //     $reservation = Reservation::find($checkIn->reservation_id);
    //     if ($reservation) {
    //         $reservation->delete();
    //     }
    // }

    /**
     * Handle the CheckIn "updated" event.
     */
    public function updated(CheckIn $checkIn): void
    {
        //
    }

    /**
     * Handle the CheckIn "deleted" event.
     */
    public function deleted(CheckIn $checkIn): void
    {
        //
    }

    /**
     * Handle the CheckIn "restored" event.
     */
    public function restored(CheckIn $checkIn): void
    {
        //
    }

    /**
     * Handle the CheckIn "force deleted" event.
     */
    public function forceDeleted(CheckIn $checkIn): void
    {
        //
    }
}
