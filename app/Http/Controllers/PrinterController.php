<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    // Retrieve all printer records
    public function index()
    {
        $printers = Printer::all();

        return response()->json([
            'data' => $printers,
            'message' => 'Data retrieved successfully'
        ]);
    }

    // Store a new printer record
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|unique:printers,name',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|integer',
            'driver' => 'required',
        ]);

        // Create a new printer
        Printer::create($request->all());

        return response()->json([
            'message' => 'Printer created successfully'
        ]);
    }

    // Update an existing printer
    public function update(Request $request, Printer $printer)
    {
        // Validation
        $request->validate([
            'name' => 'required|unique:printers,name,' . $printer->id,
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|integer',
            'driver' => 'required',
        ]);

        // Update the printer
        $printer->update($request->all());

        return response()->json([
            'message' => 'Printer updated successfully'
        ]);
    }

    // Delete a printer
    public function destroy(Printer $printer)
    {
        $printer->delete();

        return response()->json([
            'message' => 'Printer deleted successfully'
        ]);
    }
}
