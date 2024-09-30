<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
   
    public function generateInvoice(Order $order)
    {
        $order->load('orderItems.menuItem', 'guest');
        $pdf = PDF::loadView('invoices.invoice', compact('order'));
        return $pdf->stream('invoice-' . $order->id . '.pdf');
    }
}
