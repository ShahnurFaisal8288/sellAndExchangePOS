<?php

namespace App\Livewire\Product\Supplier;

use App\Models\Supplier;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.app.base.base')]
class SupplierComponent extends Component
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
        'name' => 'required|string|max:100',

        'phone' => [
            'required',
            'string',
            'max:20',
            Rule::unique('suppliers', 'phone')->ignore($this->editingId),
        ],

        'email' => 'nullable|email|max:100',
        'address' => 'nullable|string',
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
        $supplier = Supplier::findOrFail($id);

        $this->editingId = $supplier->id;
        $this->name = $supplier->name;
        $this->phone = $supplier->phone;
        $this->email = $supplier->email;
        $this->address = $supplier->address;

        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate();

        Supplier::updateOrCreate(
            ['id' => $this->editingId],
            $validated
        );

        session()->flash(
            'message',
            $this->editingId
                ? 'Supplier updated successfully.'
                : 'Supplier created successfully.'
        );

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        Supplier::findOrFail($id)->delete();

        session()->flash('message', 'Supplier deleted successfully.');
    }

    public function resetForm()
    {
        $this->reset([
            'editingId',
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
        return view('livewire.product.supplier.supplier-component', [
            'suppliers' => Supplier::query()
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
