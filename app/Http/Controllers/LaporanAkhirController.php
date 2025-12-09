<?php

namespace App\Http\Controllers;

use App\Models\LaporanAkhir;
use App\Models\MahasiswaData;
use Illuminate\Http\Request;

class LaporanAkhirController extends Controller
{
    /**
     * GET — List laporan akhir mahasiswa bimbingan
     * ROLE: Pembimbing
     */
    public function index(Request $request)
    {
        $pembimbing = $request->user()->pembimbingData;

        if (!$pembimbing) {
            return response()->json([
                "status" => false,
                "message" => "Anda tidak memiliki data pembimbing."
            ], 403);
        }

        $laporan = LaporanAkhir::with(['mahasiswa.user'])
            ->whereHas('mahasiswa', fn($q) =>
                $q->where('pembimbing_id', $pembimbing->id)
            )
            ->orderBy('updated_at', 'desc')
            ->get();


        return response()->json([
            "status" => true,
            "data" => $laporan
        ]);
    }

    /**
     * GET — Detail laporan akhir mahasiswa
     */
    public function show($id, Request $request)
    {
        $pembimbing = $request->user()->pembimbingData;

        $laporan = LaporanAkhir::with('mahasiswa.user')
            ->where('id', $id)
            ->whereHas('mahasiswa', function ($q) use ($pembimbing) {
                $q->where('pembimbing_id', $pembimbing->id);
            })
            ->firstOrFail();

        return response()->json([
            "status" => true,
            "data" => $laporan
        ]);
    }

    /**
     * POST — Verifikasi laporan akhir
     */
    public function verify($id, Request $request)
    {
        $pembimbing = $request->user()->pembimbingData;

        $laporan = LaporanAkhir::where('id', $id)
            ->whereHas('mahasiswa', fn($q) => 
                $q->where('pembimbing_id', $pembimbing->id)
            )
            ->firstOrFail();

        $laporan->update([
            "status" => "verified"
        ]);

        return response()->json([
            "status" => true,
            "message" => "Laporan akhir berhasil diverifikasi.",
            "data" => $laporan
        ]);
    }

    /**
     * POST — Tolak laporan akhir
     */
    public function reject($id, Request $request)
    {
        $request->validate([
            'alasan' => 'required|string|min:5'
        ]);

        $pembimbing = $request->user()->pembimbingData;

        $laporan = LaporanAkhir::where('id', $id)
            ->whereHas('mahasiswa', fn($q) => 
                $q->where('pembimbing_id', $pembimbing->id)
            )
            ->firstOrFail();

        $laporan->update([
            "status" => "rejected",
            "catatan" => $request->alasan
        ]);

        return response()->json([
            "status" => true,
            "message" => "Laporan akhir ditolak.",
            "data" => $laporan
        ]);
    }
}
