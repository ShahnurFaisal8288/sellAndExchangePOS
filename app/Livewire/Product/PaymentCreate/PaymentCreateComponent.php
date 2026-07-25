<?php

namespace App\Livewire\Product\PaymentCreate;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.base.base')]
class PaymentCreateComponent extends Component
{
    use WithPagination;

    // ----- Left side: search / pick a purchase with due -----
    public string $search = '';

    // ----- Right side: payment form for the selected purchase -----
    public ?int $selectedPurchaseId = null;
    public $selectedPurchase = null; // hydrated Purchase model for display

    public string $amount = '';
    public string $payment_date = '';
    public string $method = 'cash';
    public string $note = '';

    public function mount(): void
    {
        $this->payment_date = now()->format('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function selectPurchase(int $purchaseId): void
    {
        $purchase = Purchase::with('supplier')->findOrFail($purchaseId);

        if ((float) $purchase->due_amount <= 0) {
            session()->flash('error', 'This purchase has no outstanding due.');
            return;
        }

        $this->selectedPurchaseId = $purchase->id;
        $this->selectedPurchase = $purchase;
        $this->amount = number_format((float) $purchase->due_amount, 2, '.', '');
        $this->payment_date = now()->format('Y-m-d');
        $this->method = 'cash';
        $this->note = '';
        $this->resetErrorBag();
    }

    public function clearSelection(): void
    {
        $this->reset(['selectedPurchaseId', 'selectedPurchase', 'amount', 'note']);
        $this->method = 'cash';
        $this->payment_date = now()->format('Y-m-d');
        $this->resetErrorBag();
    }

    private function validateForm(): void
    {
        $errors = [];

        if (!$this->selectedPurchaseId) {
            $errors['selectedPurchaseId'] = 'Select a purchase to pay against.';
        }

        if ($this->amount === '' || $this->amount === null || (float) $this->amount <= 0) {
            $errors['amount'] = 'Enter a payment amount greater than zero.';
        }

        if ($this->selectedPurchase && (float) $this->amount > (float) $this->selectedPurchase->due_amount) {
            $errors['amount'] = 'Payment cannot exceed the outstanding due of ৳'
                . number_format((float) $this->selectedPurchase->due_amount, 2) . '.';
        }

        if (blank($this->payment_date)) {
            $errors['payment_date'] = 'Payment date is required.';
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    public function save(): void
    {
        $this->validateForm();

        DB::transaction(function () {
            // Lock the purchase row so two simultaneous payments can't both
            // pass validation against a due amount that's changing underneath them.
            $purchase = Purchase::lockForUpdate()->findOrFail($this->selectedPurchaseId);

            $amount = (float) $this->amount;

            if ($amount > (float) $purchase->due_amount) {
                // Re-check inside the lock in case due changed since page load.
                throw ValidationException::withMessages([
                    'amount' => 'Payment cannot exceed the outstanding due of ৳'
                        . number_format((float) $purchase->due_amount, 2) . '.',
                ]);
            }

            PurchasePayment::create([
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'amount' => $amount,
                'payment_date' => $this->payment_date,
                'method' => $this->method,
                'note' => $this->note ?: null,
                'user_id' => auth()->id() ?? 1,
            ]);

            $purchase->due_amount = max(0, (float) $purchase->due_amount - $amount);
            $purchase->paid_amount = (float) $purchase->paid_amount + $amount;
            $purchase->save();
        });

        session()->flash('success', 'Payment recorded successfully.');
        $this->clearSelection();
    }

    public function render()
    {
        $purchases = Purchase::with('supplier')
            ->where('due_amount', '>', 0)
            ->when($this->search, function ($query) {
                $term = "%{$this->search}%";
                $query->where(function ($q) use ($term) {
                    $q->where('invoice_no', 'like', $term)
                        ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', $term));
                });
            })
            ->latest('id')
            ->paginate(10);

        return view('livewire.product.payment-create.payment-create-component', [
            'purchases' => $purchases,
        ]);
    }
}
