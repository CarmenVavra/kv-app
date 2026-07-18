<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'public.landing')->name('landing');

Route::view('/impressum', 'legal.impressum')->name('legal.impressum');
Route::view('/agb', 'legal.agb')->name('legal.agb');
Route::view('/datenschutz', 'legal.datenschutz')->name('legal.datenschutz');
