<?php

namespace App\Http\Controllers;

use App\Services\OrderReportService;  // Ensure this service is correctly set up
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderReportController
{
    public function __invoke(Request $request, OrderReportService $orderReportService)
    {
        // Validate the incoming request data for start and end dates
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
    
        // Generate the report data using the service
        $reportData = $orderReportService->generate($validated);
    
        // Change $reports to $orders to match what your Blade view expects
        $orders = $reportData['reports'];
        $footer = $reportData['footer'];
        $header = $reportData['header'];
    
        // Create the PDF from the Blade view 'reports.order'
        $pdf = Pdf::loadView('reports.order', compact('orders', 'footer', 'header'))
            ->setPaper('a4', 'landscape'); // Set paper to landscape format
    
        // Add page numbering to the PDF
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(720, 570, 'Page {PAGE_NUM} of {PAGE_COUNT}', null, 10, [0, 0, 0]);
    
        // If the request is via AJAX, download the PDF; otherwise, stream it
        if ($request->ajax()) {
            return $pdf->download();
        }
    
        return $pdf->stream();
    }
    
    
}
