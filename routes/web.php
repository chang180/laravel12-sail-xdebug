<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

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

// 建立一個 api 分組，提供image的 CRUD API
Route::prefix('api')->group(function () {
        // 图片资源路由
        Route::apiResource('images', ImageController::class);
        Route::get('images/{image}/likes', [ImageController::class, 'getLikes']);

        // 測試使用，暫時排除 CSRF 驗證
        Route::patch('images/{image}/like', [ImageController::class, 'toggleLike'])
            ->withoutMiddleware(VerifyCsrfToken::class);
        // 新增圖片
        Route::post('images', [ImageController::class, 'store'])
            ->withoutMiddleware(VerifyCsrfToken::class);

    });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
