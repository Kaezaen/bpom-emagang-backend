<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MahasiswaData;
use App\Models\FormulirMagang;
use App\Models\LaporanAkhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    /**
     * GET — Profil Mahasiswa
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load([
            'mahasiswaData.pembimbing.user',
            'mahasiswaData.formulir',
        ]);

        return response()->json([
            'status' => true,
            'data'   => [
                'user'       => $user,
                'mahasiswa'  => $user->mahasiswaData,
                'pembimbing' => $user->mahasiswaData->pembimbing->user ?? null,
                'formulir'   => $user->mahasiswaData->formulir ?? null,
            ]
        ]);
    }


    /* ============================================================
     |                        LAPORAN AKHIR
     ============================================================ */

    public function showLaporanAkhir(Request $request)
    {
        $mhs = $request->user()->mahasiswaData;

        return response()->json([
            'status' => true,
            'data' => $mhs->laporanAkhir ?? null
        ]);
    }

    public function uploadLaporanAkhir(Request $request)
    {
        $mhs = $request->user()->mahasiswaData;

        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:10000'
        ]);

        // Jika sudah diverifikasi → mahasiswa tidak boleh upload ulang
        if ($mhs->laporanAkhir && $mhs->laporanAkhir->status === 'verified') {
            return response()->json([
                'status' => false,
                'message' => 'Laporan akhir sudah diverifikasi dan tidak dapat diganti.'
            ], 403);
        }

        // Jika sudah ada → hapus dan replace
        if ($mhs->laporanAkhir) {
            Storage::disk('public')->delete($mhs->laporanAkhir->file_laporan);
            $mhs->laporanAkhir->delete();
        }

        $path = $request->file('file')->store('laporan-akhir', 'public');

        $laporan = LaporanAkhir::create([
            'mahasiswa_id' => $mhs->id,
            'file_laporan' => $path,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Laporan akhir berhasil di-upload.',
            'data' => $laporan
        ]);
    }

    public function deleteLaporanAkhir(Request $request)
    {
        $mhs = $request->user()->mahasiswaData;

        if (!$mhs->laporanAkhir) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada laporan untuk dihapus.'
            ], 404);
        }

        // Jika sudah diverifikasi → tidak boleh hapus
        if ($mhs->laporanAkhir->status === 'verified') {
            return response()->json([
                'status' => false,
                'message' => 'Laporan yang sudah diverifikasi tidak dapat dihapus.'
            ], 403);
        }

        Storage::disk('public')->delete($mhs->laporanAkhir->file_laporan);
        $mhs->laporanAkhir->delete();

        return response()->json([
            'status' => true,
            'message' => 'Laporan akhir berhasil dihapus.'
        ]);
    }

    /* ============================================================
     |                     PROGRESS TRACKING
     ============================================================ */

    public function progress(Request $request)
    {
        $mhs = $request->user()->mahasiswaData;
        $form = $mhs->formulir;

        if (!$form) {
            return response()->json([
                'status' => true,
                'progress' => 0,
                'detail' => []
            ]);
        }

        $mulai = strtotime($form->waktu_mulai);
        $selesai = strtotime($form->waktu_selesai);
        $now = time();

        if ($now < $mulai) $now = $mulai;
        if ($now > $selesai) $now = $selesai;

        $progress = round((($now - $mulai) / ($selesai - $mulai)) * 100);

        return response()->json([
            'status' => true,
            'progress' => $progress,
            'tanggal_mulai' => $form->waktu_mulai,
            'tanggal_selesai' => $form->waktu_selesai
        ]);
    }
}
