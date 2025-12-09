<?php

namespace App\Http\Controllers;

use App\Models\LogbookHarian;
use App\Models\LaporanAkhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogbookHarianController extends Controller
{
    /**
     * POST — Upload Logbook Harian
     */
    public function store(Request $request)
    {
        $mhs = $request->user()->mahasiswaData;

        $request->validate([
            'tanggal'   => 'required|date',
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file_path' => 'required|mimes:pdf,doc,docx|max:8000'
        ]);

        // Cek apakah logbook hari itu sudah ada
        $existing = LogbookHarian::where('mahasiswa_id', $mhs->id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            return response()->json([
                'status'  => false,
                'message' => 'Logbook untuk tanggal ini sudah diisi.'
            ], 422);
        }

        // Upload file
        $path = $request->file('file_path')->store('logbook-harian', 'public');

        $logbook = LogbookHarian::create([
            'mahasiswa_id' => $mhs->id,
            'tanggal'      => $request->tanggal,
            'judul'        => $request->judul,
            'deskripsi'    => $request->deskripsi,
            'file_path'    => $path,
            'status'       => 'pending',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Logbook berhasil disimpan.',
            'data'    => $logbook
        ]);
    }

    /**
     * GET — Daftar Logbook Mahasiswa
     */
    public function index(Request $request)
    {
        $mhs = $request->user()->mahasiswaData;

        $logbook = LogbookHarian::where('mahasiswa_id', $mhs->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $logbook
        ]);
    }

    /**
     * GET — Detail Logbook
     */
    public function show(Request $request, $id)
    {
        $mhs = $request->user()->mahasiswaData;

        $logbook = LogbookHarian::where('id', $id)
            ->where('mahasiswa_id', $mhs->id)
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'data'   => $logbook
        ]);
    }

    /**
     * PUT — Update Logbook (Hanya hari yang sama & pending)
     */
    public function update(Request $request, $id)
    {
        $mhs = $request->user()->mahasiswaData;

        $logbook = LogbookHarian::where('id', $id)
            ->where('mahasiswa_id', $mhs->id)
            ->firstOrFail();

        // Cek status
        if ($logbook->status !== 'pending') {
            return response()->json([
                'status'  => false,
                'message' => 'Tidak bisa mengubah, logbook sudah diverifikasi.'
            ], 403);
        }

        // Cek apakah hari yang sama
        if ($logbook->tanggal !== now()->format('Y-m-d')) {
            return response()->json([
                'status'  => false,
                'message' => 'Logbook hanya bisa diubah pada hari yang sama.'
            ], 403);
        }

        $request->validate([
            'judul'     => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'file_path' => 'nullable|mimes:pdf,doc,docx|max:8000'
        ]);

        // Update file
        if ($request->hasFile('file_path')) {
            Storage::disk('public')->delete($logbook->file_path);
            $logbook->file_path = $request->file('file_path')->store('logbook-harian', 'public');
        }

        $logbook->update([
            'judul'     => $request->judul ?? $logbook->judul,
            'deskripsi' => $request->deskripsi ?? $logbook->deskripsi,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Logbook berhasil diperbarui.',
            'data'    => $logbook
        ]);
    }

    /**
     * DELETE — Hanya di hari yang sama
     */
    public function destroy(Request $request, $id)
    {
        $mhs = $request->user()->mahasiswaData;

        $logbook = LogbookHarian::where('id', $id)
            ->where('mahasiswa_id', $mhs->id)
            ->firstOrFail();

        if ($logbook->tanggal !== now()->format('Y-m-d')) {
            return response()->json([
                'status'  => false,
                'message' => 'Logbook hanya bisa dihapus pada hari yang sama.'
            ], 403);
        }

        Storage::disk('public')->delete($logbook->file_path);
        $logbook->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logbook berhasil dihapus.'
        ]);
    }

    /**
     * PEMBIMBING — Verifikasi Logbook
     */
    public function verify(Request $request, $id)
    {
        $pembimbing = $request->user()->pembimbingData;

        if (!$pembimbing) {
            return response()->json([
                'status' => false,
                'message' => 'Akun ini bukan pembimbing.'
            ], 403);
        }

        $logbook = LogbookHarian::with('mahasiswa')->findOrFail($id);

        if ($logbook->mahasiswa->pembimbing_id !== $pembimbing->id) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak berwenang memverifikasi logbook ini.'
            ], 403);
        }

        $logbook->update(['status' => 'verified']);

        return response()->json([
            'status' => true,
            'message' => 'Logbook berhasil diverifikasi.',
            'data' => $logbook
        ]);
    }

    public function pembimbingIndex(Request $request)
{
    $pembimbing = $request->user()->pembimbingData;

    if (!$pembimbing) {
        return response()->json([
            'status'  => false,
            'message' => 'Akun ini bukan pembimbing.'
        ], 403);
    }

    $logbook = LogbookHarian::with(['mahasiswa.user'])
        ->whereHas('mahasiswa', fn($q) =>
            $q->where('pembimbing_id', $pembimbing->id)
        )
        ->orderBy('tanggal', 'desc')
        ->get();

    return response()->json([
        'status' => true,
        'data' => $logbook
    ]);
}
}
