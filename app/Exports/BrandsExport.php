<?php

namespace App\Exports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BrandsExport implements FromArray, WithHeadings
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
        $query = Brand::withCount('products');

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        return $query->get()->map(function ($brand) {
            return [
                'ID' => $brand->id,
                'Name' => $brand->name,
                'Description' => $brand->description,
                'Products Count' => $brand->products_count,
                'Image URL' => url('storage/' . $brand->image), // Assuming images are stored in `storage/app/public`
                'Created At' => $brand->created_at->format('Y-m-d H:i:s'),
                'Updated At' => $brand->updated_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Products Count',
            'Image URL',
            'Created At',
            'Updated At',
        ];
    }
}
