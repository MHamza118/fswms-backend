<?php

namespace App\Exports;

use App\Models\Shipping;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShipmentsExport implements FromArray, WithHeadings
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
        $query = Shipping::with(['customer', 'warehouse', 'sale']);

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        return $query->get()->map(function ($shipping) {
            return [
                'ID' => $shipping->id,
                'Sale ID' => $shipping->sale_id,
                'Customer Name' => optional($shipping->customer)->name,
                'Warehouse Name' => optional($shipping->warehouse)->name,
                'Delivery DateTime' => $shipping->date_time,
                'Deliver To' => $shipping->deliver_to,
                'Address' => $shipping->address,
                'Status' => $shipping->status,
                'Description' => $shipping->description,
                'Created At' => $shipping->created_at->format('Y-m-d H:i:s'),
                'Updated At' => $shipping->updated_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Sale ID',
            'Customer Name',
            'Warehouse Name',
            'Delivery DateTime',
            'Deliver To',
            'Address',
            'Status',
            'Description',
            'Created At',
            'Updated At',
        ];
    }
}
