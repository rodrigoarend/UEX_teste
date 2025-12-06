<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'auth.login')->name('login.form');
Route::view('/register', 'auth.register')->name('register.form');

// Dashboard de contatos (Blade)
Route::view('/contacts', 'contacts.index')->name('contacts.index');