<?php

namespace App\Http\Controllers;

use App\Models\ProtesNilai;
use App\Models\PenilaianBpom;
use App\Models\PenilaianKampus;
use Illuminate\Http\Request;

class ProtesNilaiController extends Controller
{
    /**
     * MAHASISWA – Buat protes nilai
     */
    public function store(Request $request)
    {
        $mhs = $request->user()->mahasiswaData;

        $request->validate([
            'jenis'        => 'required|in:bpom,kampus',
            'referensi_id' => 'required|integer',
            'alasan'       => 'required|string|min:10',
        ]);

        // Pastikan nilai yg diprotes valid & miliknya
        if ($request->jenis === 'bpom') {
            $ref = PenilaianBpom::where('id', $request->referensi_id)
                ->where('mahasiswa_id', $mhs->id)
                ->firstOrFail();
        } else {
            $ref = PenilaianKampus::where('id', $request->referensi_id)
                ->where('mahasiswa_id', $mhs->id)
                ->firstOrFail();
        }

        $protes = ProtesNilai::create([
            'mahasiswa_id' => $mhs->id,
            'jenis'        => $request->jenis,
            'referensi_id' => $request->referensi_id,
            'alasan'       => $request->alasan,
            'status'       => 'pending'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Protes nilai berhasil dikirim.',
            'data' => $protes
        ], 201);
    }

    /**
     * MAHASISWA – Lihat semua protes nilai miliknya
     */
    public function index(Request $request)
    {
        $mhs = $request->user()->mahasiswaData;

        $protes = ProtesNilai::where('mahasiswa_id', $mhs->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $protes
        ]);
    }

    /**
     * PEMBIMBING – List protes nilai dari mahasiswa bimbingan
     */
    public function pembimbingIndex(Request $request)
    {
        $pembimbing = $request->user()->pembimbingData;

        $protes = ProtesNilai::with(['mahasiswa.user'])
            ->whereHas('mahasiswa', function ($q) use ($pembimbing) {
                $q->where('pembimbing_id', $pembimbing->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $protes
        ]);
    }

    /**
     * PEMBIMBING – Menyelesaikan atau menolak protes nilai
     */
    public function resolve(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:resolved,rejected',
            'tanggapan' => 'required|string|min:5'
        ]);

        $pembimbing = $request->user()->pembimbingData;

        $protes = ProtesNilai::with('mahasiswa')
            ->findOrFail($id);

        // Pastikan protes ini milik mahasiswa bimbingan
        if ($protes->mahasiswa->pembimbing_id !== $pembimbing->id) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak berwenang menangani protes ini.'
            ], 403);
        }

        $protes->update([
            'status' => $request->status,
            'tanggapan' => $request->tanggapan,
            'resolved_by' => $request->user()->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Protes nilai berhasil diperbarui.',
            'data' => $protes
        ]);
    }
}
