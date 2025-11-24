<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Shipping;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    /**
     * Total paid sales amount.
     */
    public function totalPaid()
    {
        $totalPaid = (float) Sale::sum('paid');

        return successResponse('Total paid sales retrieved successfully.', [
            'total_paid' => $totalPaid,
        ]);
    }

    /**
     * Total purchase cost (based on product purchase_price * qty sold).
     */
    public function totalPurchase()
    {
        $totalPurchase = (float) DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->sum(DB::raw('sale_items.qty * products.purchase_price'));

        return successResponse('Total purchase amount retrieved successfully.', [
            'total_purchase' => $totalPurchase,
        ]);
    }

    /**
     * Net profit from all sales.
     */
    public function netProfit()
    {
        $netProfit = (float) DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->sum(DB::raw('(sale_items.price - products.purchase_price) * sale_items.qty'));

        return successResponse('Net profit calculated successfully.', [
            'net_profit' => $netProfit,
        ]);
    }

    /**
     * Top products by net profit for the current year.
     */
    public function yearlySales()
    {
        $year = now()->year;

        $products = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.id as product_id',
                'products.name',
                DB::raw('SUM(sale_items.qty) as total_units_sold'),
                DB::raw('SUM((sale_items.price - products.purchase_price) * sale_items.qty) as total_net_profit')
            )
            ->whereYear('sales.created_at', $year)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_net_profit')
            ->get();

        return successResponse('Yearly product performance retrieved successfully.', [
            'products' => $products,
        ]);
    }

    /**
     * Top products by net profit for the current month.
     */
    public function monthlySales()
    {
        $now = now();

        $products = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.id as product_id',
                'products.name',
                DB::raw('SUM(sale_items.qty) as total_units_sold'),
                DB::raw('SUM((sale_items.price - products.purchase_price) * sale_items.qty) as total_net_profit')
            )
            ->whereYear('sales.created_at', $now->year)
            ->whereMonth('sales.created_at', $now->month)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_net_profit')
            ->get();

        return successResponse('Monthly product performance retrieved successfully.', [
            'products' => $products,
        ]);
    }

    /**
     * Monthly sales vs purchase graph data for the current year.
     *
     * Frontend expects response.data.data.data to be the array.
     */
    public function monthlySaleGraph()
    {
        $year = now()->year;

        $rows = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->selectRaw('DATE_FORMAT(sales.created_at, "%b") as month')
            ->selectRaw('SUM(sale_items.price * sale_items.qty) as total_sales')
            ->selectRaw('SUM(products.purchase_price * sale_items.qty) as total_purchases')
            ->whereYear('sales.created_at', $year)
            ->groupBy('month')
            ->orderByRaw('MIN(sales.created_at)')
            ->get();

        $data = $rows->map(function ($row) {
            return [
                'month' => $row->month,
                'total_sales' => (float) $row->total_sales,
                'total_purchases' => (float) $row->total_purchases,
            ];
        })->values();

        // Nested "data" to satisfy response.data.data.data on the frontend
        return successResponse('Monthly sales graph data retrieved successfully.', [
            'data' => [
                'data' => $data,
            ],
        ]);
    }

    /**
     * Top customers by total spent.
     */
    public function topCustomers()
    {
        $customers = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(
                'customers.id',
                'customers.name',
                DB::raw('SUM(sales.grand_total) as total_spent'),
                DB::raw('COUNT(sales.id) as orders_count')
            )
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->get();

        return successResponse('Top customers retrieved successfully.', [
            'customers' => $customers,
        ]);
    }

    /**
     * Monthly order status stats based on shipping status.
     */
    public function monthlyStats()
    {
        $rows = Shipping::selectRaw('DATE_FORMAT(date_time, "%b") as month')
            ->selectRaw("SUM(CASE WHEN status IN ('ordered','packed','shipped') THEN 1 ELSE 0 END) as ordered")
            ->selectRaw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered")
            ->groupBy('month')
            ->orderByRaw('MIN(date_time)')
            ->get();

        $data = $rows->map(function ($row) {
            return [
                'month' => $row->month,
                'ordered' => (int) $row->ordered,
                'delivered' => (int) $row->delivered,
            ];
        })->values();

        return successResponse('Monthly order status stats retrieved successfully.', $data);
    }

    /**
     * Low stock products based on qty_alert.
     */
    public function lowStock()
    {
        $products = Product::whereColumn('stock_quantity', '<=', 'qty_alert')
            ->where('qty_alert', '>', 0)
            ->orderBy('stock_quantity')
            ->get([
                'id',
                'name',
                'product_image',
                'stock_quantity',
                'qty_alert',
            ]);

        // Return plain array; frontend reads response.data.data as the list
        return successResponse('Low stock products retrieved successfully.', $products);
    }
}
