<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;

class SalesExportData extends Controller
{
    public function export()
    {
        return Excel::download(new SalesExport, 'sales_export.xlsx');
    }
}
