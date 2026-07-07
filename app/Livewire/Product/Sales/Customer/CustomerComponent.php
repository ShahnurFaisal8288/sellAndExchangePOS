<?php

namespace App\Livewire\Product\Sales\Customer;

use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
#[Layout('layouts.app.base.base')]

class CustomerComponent extends Component
{
    use WithPagination;

    public $search = '';

    public $name = '';
    public $phone = '';
    public $email = '';
    public $address = '';

    public $editingId = null;
    public $showModal = false;

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:100'],

            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customers', 'phone')->ignore($this->editingId),
            ],

            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('customers', 'email')->ignore($this->editingId),
            ],

            'address' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        $this->editingId = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->email = $customer->email;
        $this->address = $customer->address;

        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate();

        Customer::updateOrCreate(
            ['id' => $this->editingId],
            $validated
        );

        session()->flash(
            'message',
            $this->editingId
                ? 'Customer updated successfully.'
                : 'Customer created successfully.'
        );

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        Customer::findOrFail($id)->delete();

        session()->flash('message', 'Customer deleted successfully.');
    }

    public function resetForm()
    {
        $this->editingId = null;

        $this->reset([
            'name',
            'phone',
            'email',
            'address',
        ]);

        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function render()
    {
        return view('livewire.product.sales.customer.customer-component', [
            'customers' => Customer::query()
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                          ->orWhere('phone', 'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%");
                    });
                })
                ->latest()
                ->paginate(10),
        ]);
    }
}
