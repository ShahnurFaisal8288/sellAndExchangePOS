<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

class DashboardComponent extends Component
{
    #[Layout('layouts.app.base.base')]

    public function render()
    {
        return view('livewire.dashboard.dashboard-component');
    }
}
