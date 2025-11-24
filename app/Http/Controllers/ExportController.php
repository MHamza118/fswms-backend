<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExportService;

class ExportController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:sales,products,customers,warehouses,shipments,categories,brands',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        return $this->exportService->exportData(
            $request->input('type'),
            $request->input('from_date'),
            $request->input('to_date')
        );
    }
}
