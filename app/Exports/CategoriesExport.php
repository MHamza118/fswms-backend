<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoriesExport implements FromArray, WithHeadings
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
        $query = Category::withCount('products');

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        return $query->get()->map(function ($category) {
            return [
                'ID' => $category->id,
                'Category Code' => $category->category_code,
                'Category Name' => $category->category_name,
                'Products Count' => $category->products_count,
                'Created At' => $category->created_at->format('Y-m-d H:i:s'),
                'Updated At' => $category->updated_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Category Code',
            'Category Name',
            'Products Count',
            'Created At',
            'Updated At',
        ];
    }
}
