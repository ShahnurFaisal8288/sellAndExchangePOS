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

    /**
     * Per-invoice, per-item breakdown (mirrors the reference "Daily Profit & Loss (Invoice)" layout),
     * plus the same top-level summary totals the cards use.
     */
    public function getReportDataProperty()
    {
        $salesQuery = Sale::query();

        if ($this->startDate && $this->endDate) {
            $salesQuery->whereBetween('sale_date', [$this->startDate, $this->endDate]);
        }

        $sales = $salesQuery
            ->with(['items.product', 'customer'])
            ->orderBy('sale_date')
            ->orderBy('id')
            ->get();

        $invoices = [];

        $grossRevenue = 0;
        $totalDiscountGiven = 0;
        $totalCostOfGoods = 0;

        foreach ($sales as $sale) {
            $invoiceItems = [];
            $invoiceTotal = 0;
            $invoiceCost = 0;

            foreach ($sale->items as $item) {
                $qty = (int) $item->quantity;
                $unitPrice = (float) $item->unit_price;
                $lineTotal = (float) $item->subtotal;
                $unitCost = $item->product ? (float) $item->product->purchase_price : 0;
                $lineCost = $unitCost * $qty;
                $lineProfit = $lineTotal - $lineCost;

                $invoiceItems[] = [
                    'sku' => $item->product?->sku ?? $item->product_id,
                    'name' => $item->product?->name ?? 'Unknown Product',
                    'imei' => $item->imei_serial,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'total' => $lineTotal,
                    'cost' => $lineCost,
                    'profit_loss' => $lineProfit,
                ];

                $invoiceTotal += $lineTotal;
                $invoiceCost += $lineCost;
            }

            $invoices[] = [
                'invoice_no' => $sale->invoice_no,
                'customer_name' => $sale->customer?->name ?? 'Walk-in Customer',
                'sale_date' => $sale->sale_date,
                'items' => $invoiceItems,
                'invoice_total' => $invoiceTotal,
                'invoice_cost' => $invoiceCost,
                'invoice_profit_loss' => $invoiceTotal - $invoiceCost,
                'discount' => (float) $sale->discount,
            ];

            $grossRevenue += $invoiceTotal;
            $totalDiscountGiven += (float) $sale->discount;
            $totalCostOfGoods += $invoiceCost;
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
            'invoices' => $invoices,
            'summary' => [
                'total_sales' => $netSales + $totalDiscountGiven,
                'net_sales' => $netSales,
                'total_discount_given' => $totalDiscountGiven,
                'total_cost_of_goods' => $totalCostOfGoods,
                'total_profit' => $totalProfit,
                'total_loss' => $totalLoss,
                'net_profit_loss' => $operatingProfit,
                'total_purchases' => $totalPurchasesExpense,
            ],
        ];
    }

    public function render()
    {
        return view('livewire.reports.profit-and-loss-report-component', [
            'report' => $this->reportData,
        ]);
    }
}
