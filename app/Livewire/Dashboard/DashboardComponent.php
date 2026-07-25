<?php

namespace App\Livewire\Dashboard;

use App\Models\Product;
use Carbon\Carbon;
use DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app.base.base')]
class DashboardComponent extends Component
{
    public function render()
    {
        $today = today()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        // --- Today's snapshot ---
        $todaySummary = DB::table('sales')
            ->whereDate('sale_date', $today)
            ->selectRaw('COUNT(*) as invoice_count, COALESCE(SUM(total_amount),0) as revenue, COALESCE(SUM(paid_amount),0) as collected, COALESCE(SUM(due_amount),0) as due')
            ->first();

        // --- This month ---
        $monthRevenue = DB::table('sales')
            ->whereBetween('sale_date', [$monthStart, $monthEnd])
            ->sum('total_amount');

        // --- Outstanding balances ---
        $totalReceivableDue = DB::table('sales')->sum('due_amount');   // owed TO shop by customers
        $totalPayableDue = DB::table('purchases')->sum('due_amount'); // owed BY shop to suppliers

        // --- Inventory health ---
        $totalProducts = Product::where('status', 'active')->count();
        $outOfStockCount = Product::where('status', 'active')->where('stock_quantity', 0)->count();
        $lowStockProducts = Product::where('status', 'active')
            ->whereColumn('stock_quantity', '<=', 'min_stock_alert')
            ->orderBy('stock_quantity')
            ->limit(6)
            ->get();
        $lowStockCount = Product::where('status', 'active')
            ->whereColumn('stock_quantity', '<=', 'min_stock_alert')
            ->count();
        $stockValue = Product::where('status', 'active')
            ->selectRaw('COALESCE(SUM(stock_quantity * purchase_price),0) as value')
            ->value('value');

        // --- Recent sales (with customer + cashier names) ---
        $recentSales = DB::table('sales')
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'sales.user_id', '=', 'users.id')
            ->orderByDesc('sales.created_at')
            ->limit(8)
            ->select(
                'sales.id',
                'sales.invoice_no',
                'sales.total_amount',
                'sales.due_amount',
                'sales.payment_method',
                'sales.sale_date',
                'customers.name as customer_name',
                'users.name as cashier_name'
            )
            ->get();

        // --- Top selling products this month ---
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$monthStart, $monthEnd])
            ->groupBy('products.id', 'products.name', 'products.model')
            ->select(
                'products.name',
                'products.model',
                DB::raw('SUM(sale_items.quantity) as qty_sold'),
                DB::raw('SUM(sale_items.subtotal) as revenue')
            )
            ->orderByDesc('qty_sold')
            ->limit(5)
            ->get();

        // --- 7-day sales trend (filled for missing days) ---
        $rawTrend = DB::table('sales')
            ->whereDate('sale_date', '>=', now()->subDays(6)->toDateString())
            ->groupBy('sale_date')
            ->select('sale_date', DB::raw('SUM(total_amount) as total'))
            ->pluck('total', 'sale_date');

        $salesTrend = collect(range(6, 0))->map(function ($daysAgo) use ($rawTrend) {
            $date = Carbon::today()->subDays($daysAgo);
            $key = $date->toDateString();
            return [
                'label' => $date->format('D'),
                'date' => $key,
                'total' => (float) ($rawTrend[$key] ?? 0),
            ];
        });

        // --- Exchanges this week ---
        $exchangesThisWeek = DB::table('exchanges')
            ->whereDate('exchange_date', '>=', now()->subDays(6)->toDateString())
            ->count();

        return view('livewire.dashboard.dashboard-component', [
            'todaySummary' => $todaySummary,
            'monthRevenue' => $monthRevenue,
            'totalReceivableDue' => $totalReceivableDue,
            'totalPayableDue' => $totalPayableDue,
            'totalProducts' => $totalProducts,
            'outOfStockCount' => $outOfStockCount,
            'lowStockProducts' => $lowStockProducts,
            'lowStockCount' => $lowStockCount,
            'stockValue' => $stockValue,
            'recentSales' => $recentSales,
            'topProducts' => $topProducts,
            'salesTrend' => $salesTrend,
            'exchangesThisWeek' => $exchangesThisWeek,
        ]);
    }
}
