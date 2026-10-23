<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Expense;
use App\Models\Supplier;
use App\Models\InventoryBatch;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\CashierShift;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');
        $now = Carbon::now();

        switch ($period) {
            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today()->startOfDay();
                $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();
                break;
            case 'today':
            default:
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                break;
        }

        // 1. Sales & Profit calculations for period
        $salesQuery = Sale::whereBetween('created_at', [$startDate, $endDate]);
        $totalSales = (float) (clone $salesQuery)->sum('total_amount');
        $transactionsCount = (int) (clone $salesQuery)->count();
        $totalCogs = (float) (clone $salesQuery)->sum('cost_of_goods_sold');
        $grossProfit = $totalSales - $totalCogs;

        // 2. Operating Expenses
        $totalExpenses = (float) Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])->sum('amount');
        $netOperatingProfit = $grossProfit - $totalExpenses;

        // 3. Sales by Payment Method
        $salesByPaymentMethod = (clone $salesQuery)
            ->select('payment_method', DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // 4. Supplier Outstanding Balances
        $outstandingSupplierBalances = (float) Supplier::sum('outstanding_balance');

        // 5. Inventory Valuation
        $inventoryCostValuation = (float) InventoryBatch::where('status', 'active')
            ->selectRaw('SUM(quantity_remaining * purchase_cost) as val')
            ->value('val');

        $inventoryRetailValuation = (float) InventoryBatch::where('status', 'active')
            ->selectRaw('SUM(quantity_remaining * selling_price) as val')
            ->value('val');

        // 6. Expiry & Low Stock counts
        $todayStr = Carbon::today()->toDateString();
        $in30Days = Carbon::today()->addDays(30)->toDateString();
        $in90Days = Carbon::today()->addDays(90)->toDateString();

        $expiredBatchesCount = InventoryBatch::where('quantity_remaining', '>', 0)
            ->where('expiry_date', '<', $todayStr)
            ->count();

        $expiringSoonCount = InventoryBatch::where('quantity_remaining', '>', 0)
            ->where('status', 'active')
            ->whereBetween('expiry_date', [$todayStr, $in90Days])
            ->count();

        $quarantinedCount = InventoryBatch::where('status', 'quarantined')
            ->where('quantity_remaining', '>', 0)
            ->count();

        // Products with stock below minimum alert threshold
        $allProducts = Product::with(['batches' => function ($q) use ($todayStr) {
            $q->where('status', 'active')
              ->where(function ($sub) use ($todayStr) {
                  $sub->whereNull('expiry_date')->orWhere('expiry_date', '>=', $todayStr);
              });
        }])->get();

        $lowStockProducts = $allProducts->filter(function ($p) {
            $currentStock = $p->batches->sum('quantity_remaining');
            return $currentStock <= $p->minimum_stock_alert;
        });

        // 7. Recent Transactions
        $recentSales = Sale::with('cashier', 'customer')->latest()->take(7)->get();
        $recentPurchases = Purchase::with('supplier')->latest()->take(5)->get();

        // 8. Best-Selling Products (Last 30 Days)
        $bestSellers = SaleItem::with('product')
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total) as total_rev'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // 9. 7-Day Chart Data
        $chartLabels = [];
        $chartSales = [];
        $chartProfit = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $dayStart = $day->copy()->startOfDay();
            $dayEnd = $day->copy()->endOfDay();

            $daySale = (float) Sale::whereBetween('created_at', [$dayStart, $dayEnd])->sum('total_amount');
            $dayCogs = (float) Sale::whereBetween('created_at', [$dayStart, $dayEnd])->sum('cost_of_goods_sold');

            $chartLabels[] = $day->format('D d/m');
            $chartSales[] = $daySale;
            $chartProfit[] = ($daySale - $dayCogs);
        }

        // Active cashier shift for current user
        $activeShift = null;
        if (auth()->check()) {
            $activeShift = CashierShift::where('cashier_id', auth()->id())
                ->where('status', 'open')
                ->latest()
                ->first();
        }

        return view('dashboard.index', compact(
            'period',
            'startDate',
            'endDate',
            'totalSales',
            'transactionsCount',
            'totalCogs',
            'grossProfit',
            'totalExpenses',
            'netOperatingProfit',
            'salesByPaymentMethod',
            'outstandingSupplierBalances',
            'inventoryCostValuation',
            'inventoryRetailValuation',
            'expiredBatchesCount',
            'expiringSoonCount',
            'quarantinedCount',
            'lowStockProducts',
            'recentSales',
            'recentPurchases',
            'bestSellers',
            'chartLabels',
            'chartSales',
            'chartProfit',
            'activeShift'
        ));
    }
}
