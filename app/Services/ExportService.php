<?php

namespace App\Services;

use App\Exports\SalesExport;
use App\Exports\BrandsExport;
use App\Exports\ProductsExport;
use App\Exports\CustomersExport;
use App\Exports\WarehousesExport;
use App\Exports\CategoriesExport;
use App\Exports\ShipmentsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportService
{
    public function exportData($type, $from = null, $to = null)
    {
        switch ($type) {
            case 'sales':
                $export = new SalesExport($from, $to);
                $fileName = 'sales.xlsx';
                break;
            case 'products':
                $export = new ProductsExport($from, $to);
                $fileName = 'products.xlsx';
                break;
            case 'customers':
                $export = new CustomersExport($from, $to);
                $fileName = 'customers.xlsx';
                break;
            case 'warehouses':
                $export = new WarehousesExport($from, $to);
                $fileName = 'warehouses.xlsx';
                break;
           case 'categories':
               $export = new CategoriesExport($from, $to);
               $fileName = 'categories.xlsx';
               break;
           case 'brands':
               $export = new BrandsExport($from, $to);
               $fileName = 'brands.xlsx';
               break;
          case 'shipments':
              $export = new ShipmentsExport($from, $to);
              $fileName = 'shipments.xlsx';
              break;
            default:
                throw new \InvalidArgumentException("Invalid export type: {$type}");
        }

        return Excel::download($export, $fileName);
    }
}
