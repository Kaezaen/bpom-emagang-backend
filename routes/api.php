<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\FormulirController;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\WeeklyReportController;
use App\Http\Controllers\LogbookHarianController;
use App\Http\Controllers\LaporanAkhirController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectProgressController;
use App\Http\Controllers\ProjectSubmissionController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\EventController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

    // FORMULIR (Public)
    Route::post('/formulir', [FormulirController::class, 'store']);
    Route::post('/formulir/cek-status', [FormulirController::class, 'cekStatus']);

    // DIVISI (Public)
    Route::get('/divisi', [DivisiController::class, 'index']);
    Route::get('/divisi/{id}', [DivisiController::class, 'show']);

    // AUTH (Public)
    Route::post('/login', [AuthController::class, 'login']);

    // 🔥 EVENT PUBLIC (INI FIX 404)
    Route::get('/event', [EventController::class, 'index']);


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (SANCTUM)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // User profile check route
    Route::get('/me', function (Request $request) {
        return response()->json([
            'status' => true,
            'user' => $request->user()->load('role', 'divisi')
        ]);
    });
});



/*
|--------------------------------------------------------------------------
| ADMIN ONLY
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // ================== DIVISI ==================
    Route::post('/divisi', [DivisiController::class, 'store']);
    Route::put('/divisi/{id}', [DivisiController::class, 'update']);
    Route::delete('/divisi/{id}', [DivisiController::class, 'destroy']);

    // ================== FORMULIR ==================
    Route::get('/formulir', [FormulirController::class, 'index']);
    Route::get('/formulir/{id}', [FormulirController::class, 'show']);
    Route::post('/formulir/terima/{id}', [FormulirController::class, 'terima']);
    Route::post('/formulir/tolak/{id}', [FormulirController::class, 'tolak']);

    // ================== PEMBIMBING ==================
    Route::post('/pembimbing', [PembimbingController::class, 'store']);
    Route::put('/pembimbing/{id}', [PembimbingController::class, 'update']);
    Route::delete('/pembimbing/{id}', [PembimbingController::class, 'destroy']);
    Route::get('/pembimbing', [PembimbingController::class, 'index']);

    // Semua bimbingan dari seluruh pembimbing
    Route::get('/pembimbing/bimbingan', [PembimbingController::class, 'allBimbingan']);
    
    // ------------------- EVENT -------------------
    Route::post('/event', [EventController::class, 'store']);
    Route::put('/event/{id}', [EventController::class, 'update']);
    Route::delete('/event/{id}', [EventController::class, 'destroy']);
});

    Route::get('/admin/logbook', [LogbookHarianController::class, 'adminIndex']);
    Route::post('/admin/logbook/{id}/verify', [LogbookHarianController::class, 'verify']);




/*
|--------------------------------------------------------------------------
| PEMBIMBING ONLY
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:pembimbing'])
    ->prefix('pembimbing')
    ->group(function () {

        Route::get('/mahasiswa', [PembimbingController::class, 'mahasiswaSaya']);

        // FILTER MAHASISWA: aktif / selesai
        Route::get('/mahasiswa/filter/{status}', [PembimbingController::class, 'filterMahasiswa']);

        // LAPORAN AKHIR
        Route::get('/laporan-akhir', [LaporanAkhirController::class, 'index']);
        Route::get('/laporan-akhir/{id}', [LaporanAkhirController::class, 'show']);
        Route::post('/laporan-akhir/{id}/verify', [LaporanAkhirController::class, 'verify']);
        Route::post('/laporan-akhir/{id}/reject', [LaporanAkhirController::class, 'reject']);

        // LOGBOOK
        Route::get('/logbook', [LogbookHarianController::class, 'pembimbingIndex']);
        Route::post('/logbook/{id}/verify', [LogbookHarianController::class, 'verify']);

        // PROJECT MANAGEMENT
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::post('/projects/{id}/add-member', [ProjectController::class, 'addMember']);
        Route::delete('/projects/{projectId}/member/{memberId}', [ProjectController::class, 'removeMember']);
        Route::post('/projects/{projectId}/replace-member/{memberId}', [ProjectController::class, 'replaceMember']);
        Route::put('/projects/{projectId}/deadline', [ProjectController::class, 'updateDeadline']);
        Route::post('/projects/{projectId}/end', [ProjectController::class, 'endProject']);
        Route::get('/projects', [ProjectController::class, 'pembimbingProjects']);
        Route::get('/projects/{projectId}/progress', [ProjectProgressController::class, 'indexForPembimbing']);
        Route::post('/projects/progress/{progressId}/review', [ProjectProgressController::class, 'review']);

        // PEMBIMBING REVIEW PROJECT SUBMISSIONS
        Route::get('/projects/{projectId}/submissions', [ProjectSubmissionController::class, 'listByProject']);
        Route::post('/submissions/{id}/approve', [ProjectSubmissionController::class, 'approve']);
        Route::post('/submissions/{id}/revision', [ProjectSubmissionController::class, 'requestRevision']);

        // Penilaian BPOM & Kampus
        Route::post('/nilai/bpom', [PenilaianController::class, 'inputBPOM']);
        Route::post('/nilai/kampus', [PenilaianController::class, 'inputKampus']);

        // Lock nilai
        Route::post('/nilai/lock', [PenilaianController::class, 'lock']);

        // Protes nilai
        Route::post('/protes/{id}/tanggapi', [PenilaianController::class, 'tanggapiProtes']);

        // LIHAT SEMUA PROTES NILAI
        Route::get('/nilai/protes', [PenilaianController::class, 'listProtes']);

        // LIHAT DETAIL PROTES
        Route::get('/protes/{id}', [PenilaianController::class, 'detailProtes']);

    });


/*
|--------------------------------------------------------------------------
| MAHASISWA ONLY
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:mahasiswa'])
    ->prefix('mahasiswa')
    ->group(function () {

        // --------------------- PROFIL ---------------------
        Route::get('/profile', [MahasiswaController::class, 'profile']);


        // ----------------- LOGBOOK HARIAN -----------------
        Route::get('/logbook', [LogbookHarianController::class, 'index']);
        Route::post('/logbook', [LogbookHarianController::class, 'store']);
        Route::get('/logbook/{id}', [LogbookHarianController::class, 'show']);
        Route::put('/logbook/{id}', [LogbookHarianController::class, 'update']);
        Route::delete('/logbook/{id}', [LogbookHarianController::class, 'destroy']);

        // ----------------- LAPORAN AKHIR -----------------
        Route::get('/laporan-akhir',    [MahasiswaController::class, 'showLaporanAkhir']);
        Route::post('/laporan-akhir',   [MahasiswaController::class, 'uploadLaporanAkhir']);
        Route::delete('/laporan-akhir', [MahasiswaController::class, 'deleteLaporanAkhir']);


        // ----------------- PROGRESS TRACKING -----------------
        Route::get('/progress', [MahasiswaController::class, 'progress']);
        // ----------------- ABSENSI -----------------
        Route::get('/absensi', [AbsensiController::class, 'index']);
        // ----------------- PROJECT MANAGEMENT -----------------
        Route::get('/projects', [ProjectController::class, 'myProjects']);
        Route::post('/projects/{projectId}/progress', [ProjectProgressController::class,'store']);
        Route::get('/projects/{projectId}/progress', [ProjectProgressController::class,'index']);

        // MAHASISWA UPLOAD PROJECT SUBMISSION
        Route::post('/projects/{projectId}/submission', [ProjectSubmissionController::class, 'store']);
        Route::get('/projects/{projectId}/submission', [ProjectSubmissionController::class, 'showForStudent']);
        

        Route::get('/nilai', [PenilaianController::class, 'myScore']);
        Route::post('/nilai/protes', [PenilaianController::class, 'protes']);

    });
    
