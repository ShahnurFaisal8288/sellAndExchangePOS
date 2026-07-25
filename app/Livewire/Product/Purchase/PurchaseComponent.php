<?php

namespace App\Livewire\Product\Purchase;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItemImei;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
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
        DB::transaction(function () use ($id) {
            $purchase = Purchase::with(['items.imeis'])->findOrFail($id);

            // Reverse stock changes and release IMEIs or mark items accordingly
            foreach ($purchase->items as $item) {
                // Deduct the quantity from product stock that was originally added
                Product::where('id', $item->product_id)
                    ->lockForUpdate()
                    ->decrement('stock_quantity', $item->quantity);

                // If IMEIs were attached to this purchase item, delete or free them up
                if ($item->imeis()->exists()) {
                    PurchaseItemImei::where('purchase_item_id', $item->id)->delete();
                }
            }

            // Delete associated purchase items first to maintain referential integrity
            $purchase->items()->delete();

            // Finally, delete the purchase record itself
            $purchase->delete();
        });

        session()->flash('message', 'Purchase record and associated stock/IMEI data removed safely.');
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
