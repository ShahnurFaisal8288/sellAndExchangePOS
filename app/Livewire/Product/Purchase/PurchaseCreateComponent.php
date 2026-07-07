<?php

namespace App\Livewire\Product\Purchase;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.app.base.base')]
class PurchaseCreateComponent extends Component
{
    public $editingId = null;

    // Form fields
    public $supplier_id = '';
    public $source_type = 'supplier';
    public $invoice_no = '';
    public $purchase_date = '';
    public $paid_amount = 0;
    public $total_amount = 0;
    public $due_amount = 0;
    public $items = [];

    protected function rules()
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'source_type' => 'required|in:supplier,customer_trade_in',
            'invoice_no' => 'required|string|max:50',
            'purchase_date' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function mount($id = null)
    {
        if ($id) {
            $purchase = Purchase::with('items')->findOrFail($id);
            $this->editingId = $purchase->id;
            $this->supplier_id = $purchase->supplier_id;
            $this->source_type = $purchase->source_type;
            $this->invoice_no = $purchase->invoice_no;
            $this->purchase_date = $purchase->purchase_date->format('Y-m-d');
            $this->paid_amount = $purchase->paid_amount;
            $this->total_amount = $purchase->total_amount;
            $this->due_amount = $purchase->due_amount;

            foreach ($purchase->items as $item) {
                $this->items[] = [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ];
            }
        } else {
            $this->purchase_date = now()->format('Y-m-d');
            $this->addItem();
        }
    }

    public function addItem()
    {
        $this->items[] = ['product_id' => '', 'quantity' => 1, 'unit_price' => 0, 'subtotal' => 0];
        $this->calculateTotals();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = $parts[0];
            if (in_array($parts[1], ['quantity', 'unit_price'])) {
                $qty = (int) ($this->items[$index]['quantity'] ?? 0);
                $price = (float) ($this->items[$index]['unit_price'] ?? 0);
                $this->items[$index]['subtotal'] = number_format($qty * $price, 2, '.', '');
            }
        }
        $this->calculateTotals();
    }

    public function updatedPaidAmount()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += ((int)($item['quantity'] ?? 0)) * ((float)($item['unit_price'] ?? 0));
        }
        $this->total_amount = number_format($total, 2, '.', '');
        $this->due_amount = number_format(max(0, $total - (float)$this->paid_amount), 2, '.', '');
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $purchase = Purchase::updateOrCreate(
                ['id' => $this->editingId],
                [
                    'supplier_id' => $this->supplier_id,
                    'source_type' => $this->source_type,
                    'user_id' => auth()->id() ?? 1,
                    'invoice_no' => $this->invoice_no,
                    'total_amount' => $this->total_amount,
                    'paid_amount' => $this->paid_amount,
                    'due_amount' => $this->due_amount,
                    'purchase_date' => $this->purchase_date,
                ]
            );

            $purchase->items()->delete();
            foreach ($this->items as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }
        });

        session()->flash('message', $this->editingId ? 'Purchase transaction updated.' : 'Purchase transaction registered.');
        return $this->redirect(route('purchases.index'), navigate: true);
    }
    public function render()
    {
        return view('livewire.product.purchase.purchase-create-component', [
            'suppliers' => Supplier::all(),
            'products' => Product::all(),
        ]);
    }
}
