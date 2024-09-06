<?php
namespace App\Jobs;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireReservation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reservationId;

    /**
     * Create a new job instance.
     */
    public function __construct($reservationId)
    {
        $this->reservationId = $reservationId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $reservation = Reservation::find($this->reservationId);

        if ($reservation && $reservation->status === 'On Hold' && 
            Carbon::now()->diffInHours($reservation->created_at) >= 1) {
            // Mark the reservation as expired
            $reservation->update(['status' => 'Expired']);
            
            // Optionally notify the staff/guest about expiration
        }
    }
}
