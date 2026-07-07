<div>
    <div class="app-content">
        <div class="container-fluid">

            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-tags me-2"></i> Categories
                    </h3>
                    <button wire:click="create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Add Category
                    </button>
                </div>

                <div class="card-body">
                    <div class="mb-3" style="max-width: 320px;">
                        <input type="text" wire:model.live.debounce.300ms="search"
                               class="form-control" placeholder="Search categories...">
                    </div>

                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr wire:key="category-{{ $category->id }}">
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        <span class="badge {{ $category->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ ucfirst($category->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button wire:click="edit({{ $category->id }})" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button wire:click="delete({{ $category->id }})"
                                                wire:confirm="Delete this category?"
                                                class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $categories->links() }}
                </div>
            </div>

        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal fade @if($showModal) show d-block @endif" tabindex="-1"
         style="@if($showModal) background: rgba(0,0,0,.5) @endif">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editingId ? 'Edit' : 'Add' }} Category</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select wire:model="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="save">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
