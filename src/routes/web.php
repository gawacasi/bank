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

// Development-only public route to download CSV for a given user_id (no auth)
Route::get('/export-csv/dev-download', [MainController::class, 'exportCsvDownloadTest']);

// Public external transaction form (no auth required)
Route::get('/external/transactions/form', [MainController::class, 'externalTransactionForm']);
Route::post('/external/transactions', [MainController::class, 'externalTransactionStore']);

Route::middleware([CheckIsLogged::class])->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('home');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/deposit', [MainController::class, 'deposit'])->name('deposit');
    Route::post('/depositSub/{id}', [MainController::class, 'depositSub'])->name('depositSub');
    Route::get('/transfer', [MainController::class, 'transfer'])->name('transfer');
    Route::post('/transferSub/{id}', [MainController::class, 'transferSub'])->name('transferSub');
    Route::post('/revert/{id}', [MainController::class, 'revert'])->name('revert');
    Route::get('/export-pdf', [MainController::class, 'exportPdfPage'])->name('exportPdf.page');
    Route::get('/export-pdf/download', [MainController::class, 'exportPdfDownload'])->name('exportPdf.download');
    Route::get('/export-csv/download', [MainController::class, 'exportCsvDownload'])->name('exportCsv.download');
    Route::get('/export-transfers/download', [MainController::class, 'exportTransfersCsvDownload'])->name('exportTransfers.download');
    // Test route to download CSV for a given user_id (development only)
    Route::get('/export-csv/test-download', [MainController::class, 'exportCsvDownloadTest'])->name('exportCsv.test');
});
