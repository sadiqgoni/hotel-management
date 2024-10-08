<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\CheckIn;

class InvoicedController extends Controller
{
    public function generateInvoice($id)
    {
        // Retrieve the CheckIn data using the ID
        $checkIn = CheckIn::with('guest', 'room')->findOrFail($id);

        // Prepare data for the view
        $data = [
            'checkIn' => $checkIn,
            'restaurantCharge' => $checkIn->restaurantCharge ?? 0,
            'totalAmount' => $checkIn->total_amount ?? 0,
            'paidAmount' => $checkIn->paid_amount ?? 0,
        ];

        // Generate the invoice PDF
        $pdf = PDF::loadView('printable.invoice', $data);
        
        // Return the PDF as a download or inline view
        return $pdf->stream('invoiced.pdf');
    }
}
