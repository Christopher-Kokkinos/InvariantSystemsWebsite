<?php

use App\Http\Controllers\IntakeRecordController;
use App\Http\Middleware\LoopbackOnlyAccess;
use Illuminate\Support\Facades\Route;

Route::middleware([LoopbackOnlyAccess::class])->group(function (): void {
    Route::get('/', function () {
        return redirect()->route('intake-records.index');
    });

    Route::prefix('intake')->name('intake-records.')->group(function (): void {
        Route::get('/', [IntakeRecordController::class, 'index'])->name('index');
        Route::get('/export', [IntakeRecordController::class, 'exportFilteredCsv'])->name('export');
        Route::get('/create', [IntakeRecordController::class, 'create'])->name('create');
        Route::post('/', [IntakeRecordController::class, 'store'])->name('store');
        Route::get('/{intakeRecord}', [IntakeRecordController::class, 'show'])->name('show');
        Route::get('/{intakeRecord}/edit', [IntakeRecordController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{intakeRecord}', [IntakeRecordController::class, 'update'])->name('update');
        Route::patch('/{intakeRecord}/status', [IntakeRecordController::class, 'updateStatus'])->name('status');
    });
});
