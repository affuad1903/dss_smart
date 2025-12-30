<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use Illuminate\Http\Request;

/**
 * Controller untuk Dashboard Utama
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard utama
     */
    public function index()
    {
        // Hitung statistik
        $totalAlternatif = Alternatif::count();
        $totalKriteria = Kriteria::count();

        return view('dashboard.index', compact('totalAlternatif', 'totalKriteria'));
    }
}
