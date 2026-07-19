<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Public\ContactController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'public.landing')->name('landing');
Route::view('/preise', 'public.pricing')->name('pricing');
Route::post('/kontakt', ContactController::class)->name('contact.store');

Route::view('/impressum', 'legal.impressum')->name('legal.impressum');
Route::view('/agb', 'legal.agb')->name('legal.agb');
Route::view('/datenschutz', 'legal.datenschutz')->name('legal.datenschutz');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
