<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport implements FromArray, WithHeadings
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
        $query = Customer::query();

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        return $query->get()->map(function ($customer) {
            return [
                'ID' => $customer->id,
                'Name' => $customer->name,
                'Phone' => $customer->phone,
                'Email' => $customer->email,
                'Tax Number' => $customer->tax_number,
                'Address' => $customer->address,
                'Status' => $customer->status,
                'Created At' => $customer->created_at->format('Y-m-d H:i:s'),
                'Updated At' => $customer->updated_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Phone',
            'Email',
            'Tax Number',
            'Address',
            'Status',
            'Created At',
            'Updated At',
        ];
    }
}
