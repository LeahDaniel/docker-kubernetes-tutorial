<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

Route::get('/{code}', [LinkController::class, 'redirect'])
    ->where('code', '[a-z0-9]{6}');
