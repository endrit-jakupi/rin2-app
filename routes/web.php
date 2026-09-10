<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/login', 'pages.login');

Route::livewire('/users', 'pages.users')->middleware('auth');

Route::get('/', function () {
    return view('welcome');
});