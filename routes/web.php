<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/login', 'pages.login');

Route::livewire('/users', 'pages.users')->middleware('auth');

Route::livewire('/notifications', 'pages.notifications')->middleware('auth');

Route::livewire('/', 'pages.home')->middleware('auth');