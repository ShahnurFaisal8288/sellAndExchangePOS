<?php

namespace App\Livewire\Reports;

use App\Models\Purchase;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.app.base.base')]

class PurchaseReportComponent extends Component
{
    use WithPagination;

    public string $dateFrom;
    public string $dateTo;
    public string $sourceType = '';

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function updated($property): void
    {
        if (in_array($property, ['dateFrom', 'dateTo', 'sourceType'])) {
            $this->resetPage();
        }
    }

    private function baseQuery()
    {
        return Purchase::query()
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->when($this->sourceType, fn ($q) => $q->where('source_type', $this->sourceType));
    }
    public function render()
    {
        $purchases = (clone $this->baseQuery())
            ->with(['supplier', 'user'])
            ->latest('purchase_date')
            ->paginate(20);

        $summary = (clone $this->baseQuery())->selectRaw('
            COUNT(*) as total_invoices,
            SUM(total_amount) as total_amount,
            SUM(paid_amount) as total_paid,
            SUM(due_amount) as total_due
        ')->first();
        return view('livewire.reports.purchase-report-component', compact('purchases', 'summary'));
    }
}
