<?php

use App\Livewire\Dashboard;
use App\Livewire\OutletForm;
use App\Livewire\ProductCatalog;
use App\Livewire\VisitForm;
use App\Livewire\VisitList;
use App\Livewire\VisitReport;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('home');
Route::get('/produk', ProductCatalog::class)->name('products.catalog');
Route::get('/outlet', OutletForm::class)->name('outlet');
Route::get('/visit', VisitList::class)->name('visit');
