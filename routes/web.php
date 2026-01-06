<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\ParameterKriteriaController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PerhitunganController;

// Redirect root ke login
Route::get('/', function () {
    return redirect('/login');
});

// Routes untuk Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes yang memerlukan authentication
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Data Alternatif (Bank Sampah)
    Route::resource('alternatif', AlternatifController::class);
    
    // Data Kriteria
    Route::get('kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
    Route::get('kriteria/create', [KriteriaController::class, 'create'])->name('kriteria.create');
    Route::post('kriteria', [KriteriaController::class, 'store'])->name('kriteria.store');
    Route::get('kriteria/{kriteria}/edit', [KriteriaController::class, 'edit'])->name('kriteria.edit');
    Route::put('kriteria/{kriteria}', [KriteriaController::class, 'update'])->name('kriteria.update');
    Route::delete('kriteria/{kriteria}', [KriteriaController::class, 'destroy'])->name('kriteria.destroy');
    
    // Parameter Kriteria
    Route::resource('parameter', ParameterKriteriaController::class);
    
    // Penilaian
    Route::get('penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
    Route::get('penilaian/create', [PenilaianController::class, 'create'])->name('penilaian.create');
    Route::post('penilaian', [PenilaianController::class, 'store'])->name('penilaian.store');
    Route::delete('penilaian/{id}', [PenilaianController::class, 'destroy'])->name('penilaian.destroy');
    
    // Perhitungan dan Hasil
    Route::get('perhitungan', [PerhitunganController::class, 'index'])->name('perhitungan.index');
    Route::get('hasil', [PerhitunganController::class, 'hasil'])->name('hasil.index');
    Route::get('hasil/export-csv', [PerhitunganController::class, 'exportCsv'])->name('hasil.export.csv');
    
    // History Perhitungan
    Route::get('history', [PerhitunganController::class, 'history'])->name('history.index');
    Route::post('history/store', [PerhitunganController::class, 'simpanHistory'])->name('history.store');
    Route::get('history/{id}', [PerhitunganController::class, 'showHistory'])->name('history.show');
    Route::delete('history/{id}', [PerhitunganController::class, 'deleteHistory'])->name('history.delete');
});
