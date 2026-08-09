    <div>
        <div class="app-content">
            <div class="container-fluid">

                @if (session('message'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                    <div class="card-header d-flex justify-content-between align-items-center border-0 py-3"
                        style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        <h3 class="card-title mb-0 text-white fw-semibold">
                            <i class="bi bi-sliders me-2"></i> Attributes
                        </h3>

                        <button wire:click="create" class="btn btn-light btn-sm rounded-pill px-3 fw-medium shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Add Attribute
                        </button>
                    </div>

                    <div class="card-body p-4">

                        <div class="mb-4" style="max-width:320px;">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-3">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="search"
                                    class="form-control border-start-0 rounded-end-3"
                                    placeholder="Search attributes..."
                                >
                            </div>
                        </div>

                        <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr class="text-uppercase small text-muted">
                <th class="border-0">Name</th>
                <th class="border-0">Value</th>
                <th class="border-0 text-end">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($attributeList as $attribute)
                <tr wire:key="attribute-{{ $attribute->id }}">
                    <td>
                        <span class="badge rounded-pill text-bg-light border fw-medium px-3 py-2">
                            {{ $attribute->name }}
                        </span>
                    </td>

                    <td>
                        @if(strtolower($attribute->name) === 'color' && preg_match('/^#[0-9A-Fa-f]{6}$/', $attribute->value))
                            <div class="d-flex align-items-center gap-2">
                                <span class="d-inline-block rounded-circle border"
                                      style="width:22px; height:22px; background-color: {{ $attribute->value }};"></span>
                                <span class="text-muted small">{{ strtoupper($attribute->value) }}</span>
                            </div>
                        @else
                            {{ $attribute->value }}
                        @endif
                    </td>

                    <td class="text-end">
                        <button
                            wire:click="edit({{ $attribute->id }})"
                            class="btn btn-sm btn-outline-primary rounded-circle"
                            style="width:32px;height:32px;"
                        >
                            <i class="bi bi-pencil"></i>
                        </button>

                        <button
                            wire:click="delete({{ $attribute->id }})"
                            wire:confirm="Delete this attribute?"
                            class="btn btn-sm btn-outline-danger rounded-circle"
                            style="width:32px;height:32px;"
                        >
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        No attributes found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $attributeList->links() }}
</div>

                    </div>
                </div>

            </div>
        </div>

        {{-- Add/Edit Modal --}}
        <div class="modal fade @if($showModal) show d-block @endif"
            tabindex="-1"
            style="@if($showModal) background:rgba(0,0,0,.5); @endif">

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content rounded-4 border-0 shadow">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-semibold">
                            {{ $editingId ? 'Edit' : 'Add' }} Attribute
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            wire:click="closeModal"
                        ></button>
                    </div>

                    <div class="modal-body pt-3">

                        <div class="mb-3">
                            <label class="form-label fw-medium">Attribute Name</label>

                            <input
                                type="text"
                                wire:model.live="name"
                                list="attribute-name-suggestions"
                                class="form-control rounded-3 @error('name') is-invalid @enderror"
                                placeholder="e.g. Color, country"
                            >
                            <datalist id="attribute-name-suggestions">
                                <option value="Color">
                                <option value="Size">
                                <option value="Material">
                            </datalist>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-medium">Value</label>

                            @if($this->isColorAttribute)
                                <div class="d-flex align-items-center gap-2">
                                    <input
                                        type="color"
                                        wire:model="value"
                                        class="form-control form-control-color rounded-3"
                                        style="width:52px; height:42px;"
                                        title="Pick a color"
                                    >
                                    <input
                                        type="text"
                                        wire:model="value"
                                        placeholder="#RRGGBB"
                                        class="form-control rounded-3 @error('value') is-invalid @enderror"
                                    >
                                </div>
                                <div class="form-text">Pick a color or type a hex code directly.</div>
                            @else
                                <input
                                    type="text"
                                    wire:model="value"
                                    class="form-control rounded-3 @error('value') is-invalid @enderror"
                                >
                            @endif

                            @error('value')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="modal-footer border-0 pt-0">

                        <button
                            type="button"
                            class="btn btn-light rounded-pill px-3"
                            wire:click="closeModal"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            class="btn btn-primary rounded-pill px-4"
                            wire:click="save"
                        >
                            <i class="bi bi-check-lg me-1"></i> Save
                        </button>

                    </div>

                </div>

            </div>

        </div>
    </div>
