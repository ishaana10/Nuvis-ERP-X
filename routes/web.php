<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

if (! request()->getRequestUri() == '/login') {
    Route::redirect('/login', '/admin/login')
        ->name('login');
}
