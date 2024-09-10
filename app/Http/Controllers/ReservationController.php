<?php

namespace App\Http\Controllers;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Services\PDFService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{

    public function print(Reservation $reservation)
    {
        // Load reservation details
        $guest = $reservation->guest;
        $room = $reservation->room;
        
        // Generate the PDF with reservation details
        $pdf = PDF::loadView('reservations.slip', compact('reservation', 'guest', 'room'));
    
        // Stream or download the PDF
        return $pdf->stream('reservation-slip.pdf');
    }
    
}
