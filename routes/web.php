<?php

use App\Livewire\Dashboard\DashboardComponent;
use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', DashboardComponent::class)->name('home');
});

require __DIR__.'/settings.php';
