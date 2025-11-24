<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesExport implements FromArray, WithHeadings, WithEvents
{
    protected $summaryRowIndex;
    protected $totalPaid = 0;
    protected $totalDue = 0;
    protected $grandTotal = 0;
    protected $totalTax = 0;
    protected $totalDiscount = 0;
    protected $totalShippingCost = 0;
    protected $from;
    protected $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function array(): array
    {
        $query = Sale::with(['saleItems.product', 'customer', 'warehouse', 'user']);

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        $sales = $query->get();
        $exportData = [];

        foreach ($sales as $sale) {
            $this->grandTotal += $sale->grand_total;
            $this->totalPaid += $sale->paid;
            $this->totalDue += $sale->due;
            $this->totalTax += $sale->tax;
            $this->totalDiscount += $sale->discount;
            $this->totalShippingCost += $sale->shipping_cost;

            foreach ($sale->saleItems as $item) {
                $exportData[] = [
                    'Date' => $sale->created_at->format('Y-m-d'),
                    'Reference' => $sale->id,
                    'Added By' => $sale->user->name ?? 'N/A',
                    'Customer' => $sale->customer->name ?? 'N/A',
                    'Warehouse' => $sale->warehouse->name ?? 'N/A',
                    'Status' => $sale->status ?? 'Pending',
                    'Grand Total' => $sale->grand_total,
                    'Paid' => $sale->paid,
                    'Due' => $sale->due,
                    'Tax' => $sale->tax,
                    'Discount' => $sale->discount,
                    'Shipping Cost' => $sale->shipping_cost,
                    'Payment Status' => $sale->payment_status ?? 'Unpaid',
                    'Shipping Status' => $sale->shipping_status ?? 'Pending',
                    'Currency' => $sale->currency ?? 'N/A',
                    'Expected Delivery Date' => $sale->expected_delivery_date ?? '',
                    'Payment Method' => $sale->payment_method ?? 'N/A',
                    'Item ID' => $item->id,
                    'Product Name' => $item->product->name ?? 'N/A',
                    'Product ID' => $item->product_id,
                    'Item Price' => $item->price,
                    'Item Qty' => $item->qty,
                    'Item Subtotal' => $item->subtotal,
                ];
            }
        }

        $this->summaryRowIndex = count($exportData) + 2;

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'Date', 'Reference', 'Added By', 'Customer', 'Warehouse', 'Status',
            'Grand Total', 'Paid', 'Due', 'Tax', 'Discount', 'Shipping Cost',
            'Payment Status', 'Shipping Status', 'Currency', 'Expected Delivery Date',
            'Payment Method', 'Item ID', 'Product Name', 'Product ID', 'Item Price', 'Item Qty', 'Item Subtotal'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $row = $this->summaryRowIndex;
                $sheet = $event->sheet;

                $sheet->setCellValue("A{$row}", 'TOTALS:');
                $sheet->setCellValue("G{$row}", $this->grandTotal);
                $sheet->setCellValue("H{$row}", $this->totalPaid);
                $sheet->setCellValue("I{$row}", $this->totalDue);
                $sheet->setCellValue("J{$row}", $this->totalTax);
                $sheet->setCellValue("K{$row}", $this->totalDiscount);
                $sheet->setCellValue("L{$row}", $this->totalShippingCost);

                $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '173a36'], // dark teal
                    ],
                ]);
            },
        ];
    }
}
