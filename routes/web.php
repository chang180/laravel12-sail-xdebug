<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::get('/debug-test', function () {
    $foo = "Hello";
    $bar = "World";
    return $foo . ' ' . $bar; // 在這裡設置斷點
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
