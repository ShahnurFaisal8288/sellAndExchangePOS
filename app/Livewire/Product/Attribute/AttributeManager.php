<?php

namespace App\Livewire\Product\Attribute;

use App\Models\Attribute;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.app.base.base')]

class AttributeManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;

    public $name;
    public $value;

    protected function rules()
    {
        return [
            'name'  => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ];
    }

    // True when the "value" field should render as a color picker
    public function getIsColorAttributeProperty()
    {
        return strtolower(trim($this->name ?? '')) === 'color';
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
        $attribute = Attribute::findOrFail($id);

        $this->editingId = $attribute->id;
        $this->name = $attribute->name;
        $this->value = $attribute->value;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Attribute::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name'  => $this->name,
                'value' => $this->value,
            ]
        );

        session()->flash('message', $this->editingId ? 'Attribute updated successfully.' : 'Attribute added successfully.');

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        Attribute::findOrFail($id)->delete();
        session()->flash('message', 'Attribute deleted.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->value = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.product.attribute.attribute-manager', [
            // renamed from 'attributes' -> 'attributeList' to avoid clashing
            // with Blade's reserved $attributes (ComponentAttributeBag)
            'attributeList' => Attribute::when($this->search, function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('value', 'like', "%{$this->search}%");
                })
                ->latest()
                ->paginate(10),
        ]);
    }
}
