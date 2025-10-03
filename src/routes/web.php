<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\CheckIsLogged;
use App\Http\Middleware\CheckIsNotLogged;
use Illuminate\Support\Facades\Route;

Route::middleware([CheckIsNotLogged::class])->group(function () {
    Route::get('/login', [AuthController::class, 'login']);
    Route::get('/createAccount', [AuthController::class, 'createAccount'])->name('createAccount');
    Route::post('/createAccountSubmit', [AuthController::class, 'createAccountSubmit'])->name('createAccountSubmit');
    Route::post('/loginSub', [AuthController::class, 'loginSub']);
});

Route::middleware([CheckIsLogged::class])->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('home');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/deposit', [MainController::class, 'deposit'])->name('deposit');
    Route::post('/depositSub/{id}', [MainController::class, 'depositSub'])->name('depositSub');
    Route::get('/transfer', [MainController::class, 'transfer'])->name('transfer');
    Route::post('/transferSub/{id}', [MainController::class, 'transferSub'])->name('transferSub');
    Route::post('/revert/{id}', [MainController::class, 'revert'])->name('revert');
});
