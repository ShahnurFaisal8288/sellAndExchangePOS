<?php

namespace App\Livewire\Product\Brand;

use App\Models\Brand;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.base.base')] // adjust to match your actual layout view path
class BrandComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $name = '';
    public $status = 'active';
    public $editingId = null;
    public $showModal = false;

    protected $rules = [
        'name'   => 'required|string|max:50',
        'status' => 'required|in:active,inactive',
    ];

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
        $brand = Brand::findOrFail($id);
        $this->editingId = $brand->id;
        $this->name = $brand->name;
        $this->status = $brand->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Brand::updateOrCreate(
            ['id' => $this->editingId],
            ['name' => $this->name, 'status' => $this->status]
        );

        session()->flash('message', $this->editingId ? 'Brand updated.' : 'Brand created.');

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        $brand = Brand::findOrFail($id);

        // Guard: don't delete a brand that still has products attached
        if ($brand->products()->exists()) {
            session()->flash('error', 'Cannot delete — products are still assigned to this brand.');
            return;
        }

        $brand->delete();
        session()->flash('message', 'Brand deleted.');
    }

    public function resetForm()
    {
        $this->reset(['name', 'status', 'editingId']);
        $this->status = 'active';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.product.brand.brand-component', [
            'brands' => Brand::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate(10),
        ]);
    }
}
