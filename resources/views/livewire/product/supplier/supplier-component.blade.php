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
                        <i class="bi bi-truck me-2"></i> Suppliers
                    </h3>

                    <button wire:click="create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Add Supplier
                    </button>
                </div>

                <div class="card-body">

                    <div class="mb-3" style="max-width:320px;">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control"
                            placeholder="Search suppliers..."
                        >
                    </div>

                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($suppliers as $supplier)

                                <tr wire:key="supplier-{{ $supplier->id }}">

                                    <td>{{ $supplier->name }}</td>

                                    <td>{{ $supplier->phone }}</td>

                                    <td>{{ $supplier->email ?: '-' }}</td>

                                    <td>{{ $supplier->address ?: '-' }}</td>

                                    <td class="text-end">

                                        <button
                                            wire:click="edit({{ $supplier->id }})"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <button
                                            wire:click="delete({{ $supplier->id }})"
                                            wire:confirm="Delete this supplier?"
                                            class="btn btn-sm btn-outline-danger"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No suppliers found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>
                    </table>

                    {{ $suppliers->links() }}

                </div>
            </div>

        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal fade @if($showModal) show d-block @endif"
         tabindex="-1"
         style="@if($showModal) background:rgba(0,0,0,.5); @endif">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $editingId ? 'Edit' : 'Add' }} Supplier
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        wire:click="$set('showModal', false)"
                    ></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Supplier Name
                            </label>

                            <input
                                type="text"
                                wire:model="name"
                                class="form-control @error('name') is-invalid @enderror"
                            >

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Phone
                            </label>

                            <input
                                type="text"
                                wire:model="phone"
                                class="form-control @error('phone') is-invalid @enderror"
                            >

                            @error('phone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                wire:model="email"
                                class="form-control @error('email') is-invalid @enderror"
                            >

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                Address
                            </label>

                            <textarea
                                rows="3"
                                wire:model="address"
                                class="form-control @error('address') is-invalid @enderror"
                            ></textarea>

                            @error('address')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        wire:click="$set('showModal', false)"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        wire:click="save"
                    >
                        Save
                    </button>

                </div>

            </div>

        </div>

    </div>
</div>
