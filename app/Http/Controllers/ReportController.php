<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\InventoryBatch;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = Sale::with(['cashier', 'customer'])->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('cashier_id')) {
            $query->where('cashier_id', $request->cashier_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $sales = (clone $query)->latest()->paginate(25)->withQueryString();

        $totalSales = (float) (clone $query)->sum('total_amount');
        $totalCogs = (float) (clone $query)->sum('cost_of_goods_sold');
        $grossProfit = $totalSales - $totalCogs;
        $totalTransactions = (int) (clone $query)->count();

        // Check if CSV export requested
        if ($request->get('export') === 'csv') {
            return $this->exportSalesCsv((clone $query)->get());
        }

        return view('reports.sales', compact(
            'sales',
            'startDate',
            'endDate',
            'totalSales',
            'totalCogs',
            'grossProfit',
            'totalTransactions'
        ));
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();

        // Sales revenue & COGS
        $salesQuery = Sale::whereBetween('created_at', [$startDate, $endDate]);
        $totalSales = (float) (clone $salesQuery)->sum('total_amount');
        $totalCogs = (float) (clone $salesQuery)->sum('cost_of_goods_sold');
        $grossProfit = $totalSales - $totalCogs;

        // Operating expenses
        $expenses = Expense::with('category')
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();

        $totalExpenses = $expenses->sum('total');
        $netProfit = $grossProfit - $totalExpenses;

        // Gross Profit by Medicine
        $topProfitableMedicines = SaleItem::with('product')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('product_id', DB::raw('SUM(total) as revenue'), DB::raw('SUM(quantity * unit_cost) as cogs'), DB::raw('SUM(total - (quantity * unit_cost)) as profit'))
            ->groupBy('product_id')
            ->orderByDesc('profit')
            ->take(15)
            ->get();

        return view('reports.profit_loss', compact(
            'startDate',
            'endDate',
            'totalSales',
            'totalCogs',
            'grossProfit',
            'expenses',
            'totalExpenses',
            'netProfit',
            'topProfitableMedicines'
        ));
    }

    public function inventoryValuation()
    {
        $products = Product::with(['category', 'unit', 'batches' => function ($q) {
            $q->where('status', 'active')->where('quantity_remaining', '>', 0);
        }])->get();

        $totalCost = 0;
        $totalRetail = 0;

        foreach ($products as $p) {
            $stock = $p->batches->sum('quantity_remaining');
            $costVal = 0;
            foreach ($p->batches as $b) {
                $costVal += ($b->quantity_remaining * $b->purchase_cost);
            }
            $p->current_stock = $stock;
            $p->cost_valuation = $costVal;
            $p->retail_valuation = $stock * $p->selling_price;

            $totalCost += $costVal;
            $totalRetail += $p->retail_valuation;
        }

        $potentialMargin = $totalRetail - $totalCost;

        return view('reports.inventory_valuation', compact('products', 'totalCost', 'totalRetail', 'potentialMargin'));
    }

    public function expiryReport()
    {
        $today = Carbon::today()->toDateString();
        $in30 = Carbon::today()->addDays(30)->toDateString();
        $in60 = Carbon::today()->addDays(60)->toDateString();
        $in90 = Carbon::today()->addDays(90)->toDateString();

        $expired = InventoryBatch::with('product.unit')
            ->where('quantity_remaining', '>', 0)
            ->where('expiry_date', '<', $today)
            ->orderBy('expiry_date')
            ->get();

        $near30 = InventoryBatch::with('product.unit')
            ->where('quantity_remaining', '>', 0)
            ->where('status', 'active')
            ->whereBetween('expiry_date', [$today, $in30])
            ->orderBy('expiry_date')
            ->get();

        $near60 = InventoryBatch::with('product.unit')
            ->where('quantity_remaining', '>', 0)
            ->where('status', 'active')
            ->whereBetween('expiry_date', [$in30, $in60])
            ->orderBy('expiry_date')
            ->get();

        $near90 = InventoryBatch::with('product.unit')
            ->where('quantity_remaining', '>', 0)
            ->where('status', 'active')
            ->whereBetween('expiry_date', [$in60, $in90])
            ->orderBy('expiry_date')
            ->get();

        return view('reports.expiry', compact('expired', 'near30', 'near60', 'near90'));
    }

    private function exportSalesCsv($sales)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="njama_pharmacy_sales_' . date('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice No', 'Date', 'Cashier', 'Customer', 'Payment Method', 'Subtotal (TZS)', 'Discount', 'Total Amount (TZS)', 'COGS (TZS)', 'Gross Profit (TZS)']);

            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->invoice_number,
                    $sale->created_at->format('d/m/Y H:i'),
                    $sale->cashier ? $sale->cashier->name : 'N/A',
                    $sale->customer ? $sale->customer->name : 'Walk-in',
                    ucfirst(str_replace('_', ' ', $sale->payment_method)),
                    $sale->subtotal,
                    $sale->discount_amount,
                    $sale->total_amount,
                    $sale->cost_of_goods_sold,
                    $sale->gross_profit,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
