<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MacAddressController;

Route::get('/', function () {
    return view('welcome');
});
