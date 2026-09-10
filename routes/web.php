<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/login', 'pages.login');

Route::get('/', function () {
    return view('welcome');
});