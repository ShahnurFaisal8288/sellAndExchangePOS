<?php

namespace App\Livewire\Reports;

use App\Models\Sale;
use App\Models\Purchase;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app.base.base')]
class ProfitAndLossReportComponent extends Component
{
    public $startDate;
    public $endDate;
    public $filterType = 'custom';

    public function mount()
    {
        $this->setDateFilter('custom');
    }

    public function updatedFilterType($value)
    {
        $this->setDateFilter($value);
    }

    public function setDateFilter($type)
    {
        $this->filterType = $type;

        switch ($type) {
            case 'today':
                $this->startDate = now()->toDateString();
                $this->endDate = now()->toDateString();
                break;
            case 'this_week':
                $this->startDate = now()->startOfWeek()->toDateString();
                $this->endDate = now()->endOfWeek()->toDateString();
                break;
            case 'this_month':
                $this->startDate = now()->startOfMonth()->toDateString();
                $this->endDate = now()->endOfMonth()->toDateString();
                break;
            case 'this_year':
                $this->startDate = now()->startOfYear()->toDateString();
                $this->endDate = now()->endOfYear()->toDateString();
                break;
            case 'all':
                $this->startDate = null;
                $this->endDate = null;
                break;
            case 'custom':
                $this->startDate = $this->startDate ?: now()->toDateString();
                $this->endDate = $this->endDate ?: now()->toDateString();
                break;
        }
    }

    public function updatedStartDate($value)
    {
        if ($this->endDate && $this->startDate > $this->endDate) {
            $this->endDate = $this->startDate;
        }
    }

    public function updatedEndDate($value)
    {
        if ($this->startDate && $this->endDate < $this->startDate) {
            $this->startDate = $this->endDate;
        }
    }

    public function getReportDataProperty()
    {
        $salesQuery = Sale::query();

        if ($this->startDate && $this->endDate) {
            $salesQuery->whereBetween('sale_date', [$this->startDate, $this->endDate]);
        }

        $totalSales = (float) $salesQuery->sum('total_amount');
        $totalDiscountGiven = (float) $salesQuery->sum('discount');

        $sales = $salesQuery->with('items.product')->get();

        $totalCostOfGoods = 0;
        $grossRevenue = 0;

        foreach ($sales as $sale) {
            $grossRevenue += (float) $sale->total_amount;
            foreach ($sale->items as $item) {
                $purchasePrice = $item->product ? (float) $item->product->purchase_price : 0;
                $totalCostOfGoods += $purchasePrice * $item->quantity;
            }
        }

        $netSales = $grossRevenue;
        $operatingProfit = $netSales - $totalCostOfGoods;

        $purchasesQuery = Purchase::query();
        if ($this->startDate && $this->endDate) {
            $purchasesQuery->whereBetween('purchase_date', [$this->startDate, $this->endDate]);
        }
        $totalPurchasesExpense = (float) $purchasesQuery->sum('total_amount');

        $totalProfit = $operatingProfit > 0 ? $operatingProfit : 0;
        $totalLoss = $operatingProfit < 0 ? abs($operatingProfit) : 0;

        return [
            'total_sales' => $totalSales + $totalDiscountGiven,
            'net_sales' => $totalSales,
            'total_discount_given' => $totalDiscountGiven,
            'total_cost_of_goods' => $totalCostOfGoods,
            'total_profit' => $totalProfit,
            'total_loss' => $totalLoss,
            'net_profit_loss' => $operatingProfit,
            'total_purchases' => $totalPurchasesExpense,
        ];
    }

    public function render()
    {
        return view('livewire.reports.profit-and-loss-report-component', [
            'report' => $this->reportData,
        ]);
    }
}
