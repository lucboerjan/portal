<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::guard('web')->check()) {
        return redirect('/admin');
    }
    return redirect('/admin/login');
});