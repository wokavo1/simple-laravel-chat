<?php

use App\Http\Controllers\Chat\BroadcastController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Chat\PrivateChatController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name("index");

Route::get("/unauthorized", function () {
    return view('unauthorized');
})->name("unauthorized")->middleware("guest");

Route::controller(BroadcastController::class)->group(function () {
    Route::get("/chat/broadcast", 'index')->name("chat.broadcast.index");
    Route::post("/chat/broadcast", 'postMessage')->name("chat.broadcast.postMessage");
});

Route::controller(AuthController::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get("/auth/login", "loginPage")->name("auth.login.page");
        Route::post("/auth/login", "login")->name("auth.login");
        Route::get("/auth/register", "registerPage")->name("auth.register.page");
        Route::post("/auth/register", "register")->name("auth.register");
    });
    Route::post("/auth/logout", "logout")->name("auth.logout");
});

Route::controller(PrivateChatController::class)->group(function () {
    Route::middleware(AuthMiddleware::class)->group(function () {
        Route::get("/chat/private", "index")->name("chat.private.index");
    });

    Route::middleware(ApiAuthMiddleware::class)->group(function () {
        Route::get("/chat/private/data", "getChatsData")->name("chat.private.data");
        Route::post("/chat/private/create", "createChat")->name("chat.private.create");
        Route::get("/chat/private/test", "test")->name("chat.private.test");
    });
});