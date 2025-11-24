<?php

namespace App\Exports;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WarehousesExport implements FromArray, WithHeadings
{
    protected $from;
    protected $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function array(): array
    {
        $query = Warehouse::withCount('products');

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        return $query->get()->map(function ($warehouse) {
            return [
                'ID' => $warehouse->id,
                'Name' => $warehouse->name,
                'Country' => $warehouse->country,
                'City' => $warehouse->city,
                'Email' => $warehouse->email,
                'Zip Code' => $warehouse->zip_code,
                'Address' => $warehouse->address,
                'Phone' => $warehouse->phone,
                'Status' => $warehouse->status,
                'Products Count' => $warehouse->products_count,
                'Created At' => $warehouse->created_at->format('Y-m-d H:i:s'),
                'Updated At' => $warehouse->updated_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Country',
            'City',
            'Email',
            'Zip Code',
            'Address',
            'Phone',
            'Status',
            'Products Count',
            'Created At',
            'Updated At',
        ];
    }
}
