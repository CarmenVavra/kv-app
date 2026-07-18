<?php

use Auth0\Laravel\Controllers\CallbackController;
use Auth0\Laravel\Controllers\LoginController;
use Auth0\Laravel\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'public.landing')->name('landing');

Route::view('/impressum', 'legal.impressum')->name('legal.impressum');
Route::view('/agb', 'legal.agb')->name('legal.agb');
Route::view('/datenschutz', 'legal.datenschutz')->name('legal.datenschutz');

Route::view('/login', 'auth.login')->name('login');
Route::get('/login/auth0', LoginController::class)->name('login.authorize');
Route::get('/callback', CallbackController::class)->name('callback');
Route::get('/logout', LogoutController::class)->name('logout');
