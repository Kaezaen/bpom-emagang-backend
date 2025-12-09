<?php

namespace App\Http\Controllers;

use App\Models\FormulirMagang;
use App\Models\User;
use App\Models\MahasiswaData;
use App\Models\PembimbingData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class FormulirController extends Controller
{
    /**
     * GET LIST FORMULIR (ADMIN)
     */
    public function index(Request $request)
    {
        $query = FormulirMagang::with('divisi');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->orderBy('id','desc')->get());
    }

    /**
     * GET SINGLE FORMULIR
     */
    public function show($id)
    {
        $form = FormulirMagang::with('divisi')->findOrFail($id);
        return response()->json($form);
    }

    /**
     * SUBMIT FORMULIR MAGANG (PUBLIC)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:50|unique:formulir_magang,nik',
            'nomor_hp' => 'required|string|max:20',

            'universitas' => 'required|string',
            'alamat_universitas' => 'nullable|string',
            'jurusan' => 'required|string',
            'semester' => 'required|string',

            'divisi_id' => 'required|exists:divisi,id',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',

            'proposal' => 'required|mimes:pdf|max:3000',
            'surat_permohonan' => 'required|mimes:pdf|max:3000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $proposalPath = $request->file('proposal')->store('formulir/proposal', 'public');
        $suratPath = $request->file('surat_permohonan')->store('formulir/permohonan', 'public');

        $form = FormulirMagang::create([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'nomor_hp' => $request->nomor_hp,
            'universitas' => $request->universitas,
            'alamat_universitas' => $request->alamat_universitas,
            'jurusan' => $request->jurusan,
            'semester' => $request->semester,
            'divisi_id' => $request->divisi_id,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'proposal' => $proposalPath,
            'surat_permohonan' => $suratPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Pendaftaran berhasil dikirim.',
            'data' => $form
        ], 201);
    }

    /**
     * CEK STATUS FORMULIR (PUBLIC)
     */
    public function cekStatus(Request $request)
    {
        $request->validate([
            'nik' => 'required|string'
        ]);

        $form = FormulirMagang::with('divisi')->where('nik', $request->nik)->first();

        if (!$form) {
            return response()->json([
                'status' => false,
                'message' => 'Data pendaftaran tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'nama' => $form->nama,
                'nik' => $form->nik,
                'status' => $form->status,
                'divisi' => $form->divisi->nama_divisi,
                'waktu_mulai' => $form->waktu_mulai,
                'waktu_selesai' => $form->waktu_selesai,
            ]
        ]);
    }

    /**
     * TERIMA FORMULIR (ADMIN)
     */
    public function terima(Request $request, $id)
    {
        $request->validate([
            'pembimbing_id' => 'nullable|exists:pembimbing_data,id'
        ]);

        $form = FormulirMagang::findOrFail($id);

        if ($form->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'Formulir sudah diproses sebelumnya.'
            ], 422);
        }

        $form->update(['status' => 'diterima']);

        $password = 'magang123';

        // Create user mahasiswa
        $user = User::create([
            'name' => $form->nama,
            'email' => strtolower($form->nik) . '@student.com',
            'phone' => $form->nomor_hp,
            'password' => Hash::make($password),
            'role_id' => 3,
            'is_active' => true,
        ]);

        // assign pembimbing jika ada
        $pembimbing = $request->pembimbing_id;

        $mahasiswaData = MahasiswaData::create([
            'user_id' => $user->id,
            'pembimbing_id' => $pembimbing,
            'formulir_id' => $form->id,
            'status' => 'aktif',
            'phone' => $form->nomor_hp,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Mahasiswa berhasil diterima.',
            'login_akun' => [
                'email' => $user->email,
                'password_default' => $password
            ],
            'mahasiswa_data' => $mahasiswaData
        ]);
    }

    /**
     * TOLAK FORMULIR (ADMIN)
     */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|min:5'
        ]);

        $form = FormulirMagang::findOrFail($id);

        if ($form->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'Formulir sudah diproses sebelumnya.'
            ], 422);
        }

        $form->update([
            'status' => 'ditolak',
            'alasan_tolak' => $request->alasan
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Formulir berhasil ditolak.',
            'data' => $form
        ]);
    }
}
