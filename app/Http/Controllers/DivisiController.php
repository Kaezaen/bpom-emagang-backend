<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    // Ambil semua divisi (public)
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Divisi::all()
        ]);
    }

    // Detail divisi tertentu
    public function show($id)
    {
        $divisi = Divisi::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $divisi
        ]);
    }

    // Tambah divisi (admin only)
    public function store(Request $request)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kuota' => 'required|integer|min:0',
        ]);

        $divisi = Divisi::create([
        'nama_divisi' => $request->nama_divisi,
        'deskripsi' => $request->deskripsi,
        'kuota' => $request->kuota,
    ]);

        return response()->json([
        'status' => true,
        'message' => 'Divisi berhasil ditambahkan',
        'data' => $divisi
    ], 201);
}


    // Update divisi (admin only)
    public function update(Request $request, $id)
    {
        $divisi = Divisi::findOrFail($id);

        $request->validate([
            'nama_divisi' => 'required|string|max:255',
            'kuota' => 'required|integer|min:0',
        ]);

        $divisi->update([
            'nama_divisi' => $request->nama_divisi,
            'deskripsi' => $request->deskripsi,
            'kuota' => $request->kuota,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Divisi berhasil diperbarui.',
            'data' => $divisi
        ]);
    }

    // Hapus divisi (admin only)
    public function destroy($id)
    {
        Divisi::destroy($id);

        return response()->json([
            'status' => true,
            'message' => 'Divisi berhasil dihapus.'
        ]);
    }
}
