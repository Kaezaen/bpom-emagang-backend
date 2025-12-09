<?php

namespace App\Http\Controllers;

use App\Models\MahasiswaData;
use App\Models\PenilaianBPOM;
use App\Models\PenilaianKampus;
use App\Models\ProtesNilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PEMBIMBING INPUT NILAI BPOM
    |--------------------------------------------------------------------------
    */
    public function inputBPOM(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa_data,id',
            'kehadiran' => 'required|integer|min:0|max:100',
            'taat_jadwal' => 'required|integer|min:0|max:100',
            'pemahaman_materi' => 'required|integer|min:0|max:100',
            'praktek_kerja' => 'required|integer|min:0|max:100',
            'komunikasi' => 'required|integer|min:0|max:100',
            'laporan' => 'required|integer|min:0|max:100',
            'presentasi' => 'required|integer|min:0|max:100',
        ]);

        $pembimbing = $request->user()->pembimbingData;

        // filter mahasiswa bimbingan
        $mhs = MahasiswaData::where('id', $request->mahasiswa_id)
            ->where('pembimbing_id', $pembimbing->id)
            ->firstOrFail();

        $nilaiAkhir = (
            $request->kehadiran +
            $request->taat_jadwal +
            $request->pemahaman_materi +
            $request->praktek_kerja +
            $request->komunikasi +
            $request->laporan +
            $request->presentasi
        ) / 7;

        $nilai = PenilaianBPOM::updateOrCreate(
            ['mahasiswa_id' => $mhs->id],
            [
                'kehadiran' => $request->kehadiran,
                'taat_jadwal' => $request->taat_jadwal,
                'pemahaman_materi' => $request->pemahaman_materi,
                'praktek_kerja' => $request->praktek_kerja,
                'komunikasi' => $request->komunikasi,
                'laporan' => $request->laporan,
                'presentasi' => $request->presentasi,
                'nilai_akhir' => round($nilaiAkhir, 2)
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Nilai BPOM berhasil disimpan',
            'data' => $nilai
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PEMBIMBING INPUT NILAI KAMPUS
    |--------------------------------------------------------------------------
    */
    public function inputKampus(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa_data,id',
            'nilai_akhir' => 'required|integer|min:0|max:100'
        ]);

        $pembimbing = $request->user()->pembimbingData;

        $mhs = MahasiswaData::where('id', $request->mahasiswa_id)
            ->where('pembimbing_id', $pembimbing->id)
            ->firstOrFail();

        $nilai = PenilaianKampus::updateOrCreate(
            ['mahasiswa_id' => $mhs->id],
            ['nilai_akhir' => $request->nilai_akhir]
        );

        return response()->json([
            'status' => true,
            'message' => 'Nilai Kampus berhasil disimpan',
            'data' => $nilai
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PEMBIMBING LOCK NILAI
    |--------------------------------------------------------------------------
    */
    public function lock(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa_data,id'
        ]);

        $pembimbing = $request->user()->pembimbingData;

        $mhs = MahasiswaData::where('id', $request->mahasiswa_id)
            ->where('pembimbing_id', $pembimbing->id)
            ->firstOrFail();

        $mhs->update(['nilai_locked' => true]);

        return response()->json([
            'status' => true,
            'message' => 'Nilai mahasiswa berhasil dikunci.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MAHASISWA MELIHAT NILAI
    |--------------------------------------------------------------------------
    */
    public function myScore(Request $request)
    {
        $mhs = $request->user()->mahasiswaData;

        return response()->json([
            'status' => true,
            'bpom'   => PenilaianBPOM::where('mahasiswa_id', $mhs->id)->first(),
            'kampus' => PenilaianKampus::where('mahasiswa_id', $mhs->id)->first(),
            'locked' => $mhs->nilai_locked
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MAHASISWA PROTES NILAI
    |--------------------------------------------------------------------------
    */
    public function protes(Request $request)
    {
        $mhs = $request->user()->mahasiswaData;

        $request->validate([
            'jenis' => 'required|in:bpom,kampus',
            'referensi_id' => 'required|integer',
            'alasan' => 'required|string|min:5'
        ]);

        $protes = ProtesNilai::create([
            'mahasiswa_id' => $mhs->id,
            'jenis' => $request->jenis,
            'referensi_id' => $request->referensi_id,
            'alasan' => $request->alasan,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Protes nilai berhasil dikirim',
            'data' => $protes
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PEMBIMBING MELIHAT SEMUA PROTES NILAI MAHASISWA BIMBINGAN
    |--------------------------------------------------------------------------
    */
    public function listProtes(Request $request)
    {
        $pembimbing = $request->user()->pembimbingData;

        $protes = ProtesNilai::with(['mahasiswa.user'])
            ->whereHas('mahasiswa', fn($q)=> $q->where('pembimbing_id', $pembimbing->id))
            ->orderBy('created_at','desc')
            ->get();

        return response()->json([
            'status'=>true,
            'data'=>$protes
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PEMBIMBING MELIHAT DETAIL PROTES
    |--------------------------------------------------------------------------
    */
    public function detailProtes(Request $request, $id)
    {
        $pembimbing = $request->user()->pembimbingData;

        $protes = ProtesNilai::with('mahasiswa.user')
            ->where('id', $id)
            ->whereHas('mahasiswa', fn($q)=> $q->where('pembimbing_id',$pembimbing->id))
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'data' => $protes
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PEMBIMBING MENANGGAPI PROTES
    |--------------------------------------------------------------------------
    */
    public function tanggapiProtes(Request $request, $protesId)
    {
        $pembimbing = $request->user()->pembimbingData;

        $request->validate([
            'status' => 'required|in:resolved,rejected',
            'tanggapan' => 'required|string|min:5',
            'nilai_baru' => 'nullable|array'
        ]);

        $protes = ProtesNilai::findOrFail($protesId);

        // Pembimbing hanya boleh merespon nilai mahasiswa bimbingannya
        if ($protes->mahasiswa->pembimbing_id !== $pembimbing->id) {
            return response()->json(['status'=>false,'message'=>'Tidak berwenang'],403);
        }

        $nilaiDiubah = false;

        // ---------------------------
        // 1. Jika protes terkait nilai BPOM
        // ---------------------------
        if ($protes->jenis === 'bpom' && $request->has('nilai_baru')) {

            $nilai = PenilaianBPOM::findOrFail($protes->referensi_id);

            $nilai->update($request->nilai_baru);

            // Hitung nilai akhir otomatis
            $nilai->nilai_akhir = round(
                (
                    $nilai->kehadiran +
                    $nilai->taat_jadwal +
                    $nilai->pemahaman_materi +
                    $nilai->praktek_kerja +
                    $nilai->komunikasi +
                    $nilai->laporan +
                    $nilai->presentasi
                ) / 7
            );

            $nilai->save();

            $nilaiDiubah = true;
        }

        // ---------------------------
        // 2. Jika protes terkait nilai Kampus
        // ---------------------------
        if ($protes->jenis === 'kampus' && $request->has('nilai_baru')) {

            $nilai = PenilaianKampus::findOrFail($protes->referensi_id);

            $nilai->update([
                'nilai_akhir' => $request->nilai_baru['nilai_akhir']
            ]);

            $nilaiDiubah = true;
        }

        // ---------------------------
        // 3. Update status protes
        // ---------------------------
        $protes->update([
            'status' => $request->status,
            'tanggapan' => $request->tanggapan,
            'nilai_diubah' => $nilaiDiubah,
            'resolved_by' => $request->user()->id
        ]);

        return response()->json([
            'status' => true,
            'message' => $nilaiDiubah 
                ? 'Protes diselesaikan dan nilai telah diperbarui.'
                : 'Protes diselesaikan tanpa perubahan nilai.',
            'protes' => $protes
        ]);
    }
}
