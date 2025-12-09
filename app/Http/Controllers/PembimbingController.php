<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PembimbingData;
use App\Models\MahasiswaData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PembimbingController extends Controller
{
    /**
     * GET — List semua pembimbing (ADMIN)
     */
    public function index()
    {
        $pembimbing = User::where('role_id', 2)
            ->with(['pembimbingData.divisi'])
            ->get()
            ->map(function ($p) {
                $pd = $p->pembimbingData;

                return [
                    'id'          => $p->id,
                    'name'        => $p->name,
                    'email'       => $p->email,
                    'phone'       => $pd->phone ?? $p->phone ?? '-',
                    'divisi'      => $pd->divisi->nama_divisi ?? '-',
                    'jabatan'     => $pd->jabatan ?? '-',
                    'total_user'  => MahasiswaData::where('pembimbing_id', $pd->id ?? 0)->count(),
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => $pembimbing
        ]);
    }

    /**
     * POST — Tambah pembimbing (ADMIN)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string',
            'email'     => 'required|email|unique:users',
            'phone'     => 'nullable|string',
            'divisi_id' => 'nullable|exists:divisi,id',
            'jabatan'   => 'nullable|string',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => Hash::make('bpom123'),
            'role_id'   => 2,
            'is_active' => true,
        ]);

        $pembimbingData = PembimbingData::create([
            'user_id'   => $user->id,
            'divisi_id' => $request->divisi_id,
            'jabatan'   => $request->jabatan,
            'phone'     => $request->phone,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Pembimbing berhasil ditambahkan.',
            'data'    => [
                'user'            => $user,
                'pembimbing_data' => $pembimbingData
            ]
        ], 201);
    }

    /**
     * PUT — Update pembimbing (ADMIN)
     */
    public function update(Request $request, $id)
    {
        $pembimbing = User::with('pembimbingData')->findOrFail($id);

        $request->validate([
            'name'      => 'required|string',
            'phone'     => 'nullable|string',
            'divisi_id' => 'nullable|exists:divisi,id',
            'jabatan'   => 'nullable|string',
        ]);

        // Update user
        $pembimbing->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        // Update / create pembimbing_data
        $pd = $pembimbing->pembimbingData;

        if ($pd) {
            $pd->update([
                'divisi_id' => $request->divisi_id,
                'jabatan'   => $request->jabatan,
                'phone'     => $request->phone,
            ]);
        } else {
            $pd = PembimbingData::create([
                'user_id'   => $pembimbing->id,
                'divisi_id' => $request->divisi_id,
                'jabatan'   => $request->jabatan,
                'phone'     => $request->phone,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Pembimbing berhasil diperbarui.',
            'data'    => [
                'user'            => $pembimbing,
                'pembimbing_data' => $pd->load('divisi')
            ]
        ]);
    }

    /**
     * DELETE — Hapus pembimbing (ADMIN)
     */
    public function destroy($id)
    {
        $pembimbing = User::findOrFail($id);

        $mahasiswaAktif = MahasiswaData::where('pembimbing_id', function ($q) use ($id) {
            $q->select('id')
              ->from('pembimbing_data')
              ->where('user_id', $id);
        })
        ->where('status', 'aktif')
        ->count();

        if ($mahasiswaAktif > 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Tidak bisa menghapus pembimbing. Masih membimbing mahasiswa aktif.'
            ], 422);
        }

        $pembimbing->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Pembimbing berhasil dihapus.'
        ]);
    }

    /**
     * GET — List mahasiswa yang dibimbing (pembimbing sendiri)
     * (ROLE: Pembimbing)
     */
    public function mahasiswaSaya(Request $request)
    {
        $user = $request->user();
        $pd = $user->pembimbingData;

        if (!$pd) {
            return response()->json([
                'status' => true,
                'data'   => []
            ]);
        }

        $mahasiswa = MahasiswaData::with(['user', 'formulir'])
            ->where('pembimbing_id', $pd->id)
            ->get()
            ->map(fn ($m) => [
                'id'      => $m->user->id,
                'nama'    => $m->user->name,
                'email'   => $m->user->email,
                'phone'   => $m->phone ?? $m->user->phone,
                'status'  => $m->status,
                'formulir' => $m->formulir ? [
                    'nik'           => $m->formulir->nik,
                    'universitas'   => $m->formulir->universitas,
                    'jurusan'       => $m->formulir->jurusan,
                    'waktu_mulai'   => $m->formulir->waktu_mulai,
                    'waktu_selesai' => $m->formulir->waktu_selesai,
                ] : null
            ]);

        return response()->json([
            'status' => true,
            'data'   => $mahasiswa
        ]);
    }

    public function allBimbingan()
    {
        // Ambil semua pembimbing yang punya data pembimbing
        $pembimbingList = PembimbingData::with([
            'user',
            'divisi',
            'mahasiswa.user',
            'mahasiswa.formulir'
        ])->get();

        $result = $pembimbingList->map(function ($p) {

            return [
                'pembimbing' => [
                    'id'        => $p->id,
                    'nama'      => $p->user->name,
                    'email'     => $p->user->email,
                    'phone'     => $p->phone,
                    'divisi'    => $p->divisi->nama_divisi ?? '-',
                    'jabatan'   => $p->jabatan ?? '-',
                ],

                'mahasiswa' => $p->mahasiswa->map(function ($m) {
                    return [
                        'id'     => $m->user->id,
                        'nama'   => $m->user->name,
                        'email'  => $m->user->email,
                        'status' => $m->status,
                        'formulir' => $m->formulir ? [
                            'universitas'    => $m->formulir->universitas,
                            'jurusan'        => $m->formulir->jurusan,
                            'waktu_mulai'    => $m->formulir->waktu_mulai,
                            'waktu_selesai'  => $m->formulir->waktu_selesai,
                        ] : null
                    ];
                })
            ];
        });

        return response()->json([
            "status" => true,
            "total_pembimbing" => $result->count(),
            "data" => $result
        ]);
    }

    public function filterMahasiswa($status, Request $request)
    {
        $pembimbing = $request->user()->pembimbingData;

        if (!$pembimbing) {
            return response()->json([
                'status' => false,
                'message' => 'Akun ini bukan pembimbing.'
            ], 403);
        }

        if (!in_array($status, ['aktif', 'selesai'])) {
            return response()->json([
                'status' => false,
                'message' => 'Status tidak valid. Gunakan: aktif / selesai.'
            ], 422);
        }

        $data = MahasiswaData::with(['user', 'formulir'])
            ->where('pembimbing_id', $pembimbing->id)
            ->where('status', $status)
            ->get();

        return response()->json([
            'status' => true,
            'filter' => $status,
            'data' => $data
        ]);
    }

}