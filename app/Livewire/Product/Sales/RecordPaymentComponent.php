<?php

namespace App\Livewire\Product\Sales;

use App\Models\Sale;
use App\Models\SalePayment;
use Auth;
use DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.app.base.base')]

class RecordPaymentComponent extends Component
{
    public Sale $sale;

    public float $amount = 0;
    public string $paymentMethod = 'cash';
    public string $notes = '';

    public function mount(Sale $sale)
    {
        $this->sale = $sale;
    }

    public function save()
    {
        $this->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $this->sale->due_amount,
            'paymentMethod' => 'required|in:cash,card,mobile_banking',
        ], [
            'amount.max' => 'Amount cannot exceed the due balance of ৳' . number_format($this->sale->due_amount, 2),
        ]);

        DB::transaction(function () {
            SalePayment::create([
                'sale_id' => $this->sale->id,
                'user_id' => Auth::id(),
                'amount' => $this->amount,
                'payment_method' => $this->paymentMethod,
                'paid_date' => now()->toDateString(),
                'notes' => $this->notes ?: null,
            ]);

            $this->sale->increment('paid_amount', $this->amount);
            $this->sale->decrement('due_amount', $this->amount);
        });

        session()->flash('success', 'Payment of ৳' . number_format($this->amount, 2) . ' recorded.');

        $this->reset(['amount', 'notes']);
        $this->paymentMethod = 'cash';
        $this->sale->refresh();

        $this->dispatch('payment-recorded');
    }

    public function render()
    {
        return view('livewire.product.sales.record-payment-component');
    }
}
