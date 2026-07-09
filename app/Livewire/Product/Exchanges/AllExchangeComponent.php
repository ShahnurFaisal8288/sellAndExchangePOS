<?php

namespace App\Livewire\Product\Exchanges;

use App\Models\Exchange;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.app.base.base')]
class AllExchangeComponent extends Component
{
    use WithPagination;

    public string $typeFilter = '';
    public function render()
    {
        $exchanges = Exchange::with(['sale.customer', 'purchase', 'oldProduct', 'newProduct'])
            ->when($this->typeFilter, fn ($q) => $q->where('exchange_type', $this->typeFilter))
            ->latest('exchange_date')
            ->paginate(15);
        return view('livewire.product.exchanges.all-exchange-component', compact('exchanges'));
    }
}
