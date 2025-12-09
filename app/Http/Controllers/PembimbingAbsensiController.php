<?php

namespace App\Http\Controllers;

use App\Models\LogbookHarian;
use App\Models\MahasiswaData;
use Illuminate\Http\Request;

class PembimbingAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $pembimbing = $request->user()->pembimbingData;

        if (!$pembimbing) {
            return response()->json([
                'status' => false,
                'message' => 'Akun ini bukan pembimbing.'
            ], 403);
        }

        // ambil mahasiswa bimbingan
        $mahasiswaList = MahasiswaData::with(['user'])
            ->where('pembimbing_id', $pembimbing->id)
            ->get();

        $result = [];

        foreach ($mahasiswaList as $mhs) {
            $logbook = LogbookHarian::where('mahasiswa_id', $mhs->id)
                ->orderBy('tanggal', 'asc')
                ->get();

            $result[] = [
                'mahasiswa' => [
                    'id'   => $mhs->id,
                    'nama' => $mhs->user->name,
                ],
                'absensi' => $logbook->map(function ($l) {
                    return [
                        'tanggal' => $l->tanggal,
                        'status'  => $l->status === 'verified' ? 'hadir' : 'tidak_hadir',
                        'logbook_id' => $l->id
                    ];
                })
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $result
        ]);
    }
}
