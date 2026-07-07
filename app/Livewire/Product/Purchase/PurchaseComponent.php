<?php

namespace App\Livewire\Product\Purchase;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.app.base.base')]
class PurchaseComponent extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Purchase::findOrFail($id)->delete();
        session()->flash('message', 'Purchase record removed safely.');
    }

    public function render()
    {
        return view('livewire.product.purchase.purchase-component', [
            'purchases' => Purchase::with(['supplier', 'user'])
                ->when($this->search, function($q) {
                    $q->where('invoice_no', 'like', "%{$this->search}%")
                      ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', "%{$this->search}%"));
                })
                ->latest()
                ->paginate(10),
        ]);
    }
}
