<?php

namespace App\Http\Controllers;

use App\Models\LogbookHarian;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    /**
     * GET — Rekap Absensi Mahasiswa Berdasarkan Logbook
     */
    public function index(Request $request)
    {
        $mhs = $request->user()->mahasiswaData;
        $form = $mhs->formulir;

        if (!$form) {
            return response()->json([
                "status" => true,
                "absensi" => []
            ]);
        }

        $mulai = strtotime($form->waktu_mulai);
        $selesai = strtotime($form->waktu_selesai);

        $period = [];
        for ($t = $mulai; $t <= $selesai; $t += 86400) {
            $period[] = date("Y-m-d", $t);
        }

        // Ambil semua logbook
        $logbooks = LogbookHarian::where('mahasiswa_id', $mhs->id)
            ->get()
            ->keyBy('tanggal');

        $absensi = [];

        foreach ($period as $tanggal) {

            if ($logbooks->has($tanggal)) {
                $log = $logbooks[$tanggal];

                $status = match ($log->status) {
                    'verified' => 'hadir',
                    'pending'  => 'menunggu_verifikasi',
                    default    => 'tidak_hadir'
                };

                $absensi[] = [
                    "tanggal" => $tanggal,
                    "status" => $status,
                    "logbook_id" => $log->id
                ];
            } else {
                $absensi[] = [
                    "tanggal" => $tanggal,
                    "status" => "tidak_hadir",
                    "logbook_id" => null
                ];
            }
        }

        return response()->json([
            "status" => true,
            "absensi" => $absensi
        ]);
    }
}
