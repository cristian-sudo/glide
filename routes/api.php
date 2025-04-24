<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MacAddressController;

Route::get('/mac-address/{mac?}', [MacAddressController::class, 'lookupSingle'])->name('mac-address.lookup');
Route::post('/mac-addresses', [MacAddressController::class, 'lookupMultiple'])->name('mac-addresses.lookup');
