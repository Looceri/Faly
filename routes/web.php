<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatGptController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Chat');
    })->name('chat');;
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/chat', [ChatGptController::class, 'sendMessage'])->name('send.message');
});

require __DIR__ . '/auth.php';
