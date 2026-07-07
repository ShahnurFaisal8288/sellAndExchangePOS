<?php

namespace App\Livewire\Product\Category;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// adjust to match your actual layout view path
class CategoryComponent extends Component
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
        $category = Category::findOrFail($id);
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->status = $category->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Category::updateOrCreate(
            ['id' => $this->editingId],
            ['name' => $this->name, 'status' => $this->status]
        );

        session()->flash('message', $this->editingId ? 'Category updated.' : 'Category created.');

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);

        // Guard: don't delete a category that still has products attached
        if ($category->products()->exists()) {
            session()->flash('error', 'Cannot delete — products are still assigned to this category.');
            return;
        }

        $category->delete();
        session()->flash('message', 'Category deleted.');
    }

    public function resetForm()
    {
        $this->reset(['name', 'status', 'editingId']);
        $this->status = 'active';
        $this->resetErrorBag();
    }
    #[Layout('layouts.app.base.base')]


    public function render()
    {
        return view('livewire.product.category.category-component', [
            'categories' => Category::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate(10),
        ]);
    }
}
