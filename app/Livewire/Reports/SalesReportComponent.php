<?php

namespace App\Livewire\Reports;

use App\Models\Sale;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.app.base.base')]

class SalesReportComponent extends Component
{
     use WithPagination;

    public string $dateFrom;
    public string $dateTo;
    public string $paymentMethod = '';

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function updated($property): void
    {
        if (in_array($property, ['dateFrom', 'dateTo', 'paymentMethod'])) {
            $this->resetPage();
        }
    }

    private function baseQuery()
    {
        return Sale::query()
            ->whereBetween('sale_date', [$this->dateFrom, $this->dateTo])
            ->when($this->paymentMethod, fn ($q) => $q->where('payment_method', $this->paymentMethod));
    }

    public function render()
    {
        $sales = (clone $this->baseQuery())
            ->with(['customer', 'user'])
            ->latest('sale_date')
            ->paginate(20);

        $summary = (clone $this->baseQuery())->selectRaw('
            COUNT(*) as total_invoices,
            SUM(total_amount) as total_amount,
            SUM(discount) as total_discount,
            SUM(paid_amount) as total_paid,
            SUM(due_amount) as total_due
        ')->first();
        return view('livewire.reports.sales-report-component', compact('sales', 'summary'));
    }
}
