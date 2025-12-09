<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\MahasiswaData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    // PEMBIMBING — buat project baru dan assign anggota
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date|after:today',
            'members' => 'required|array|min:1',
            'members.*' => 'required|exists:mahasiswa_data,id'
        ]);

        $pembimbing = $request->user()->pembimbingData;
        if (!$pembimbing) {
            return response()->json(['status'=>false,'message'=>'Akun bukan pembimbing'],403);
        }

        // Cek aturan: jika mahasiswa yg ditambahkan sudah punya project active, tolak (rule 5)
        $blocked = [];
        foreach ($request->members as $mid) {
            $active = ProjectMember::where('mahasiswa_id', $mid)
                ->whereHas('project', fn($q)=> $q->where('status','active'))
                ->exists();
            if ($active) $blocked[] = $mid;
        }

        if (count($blocked) > 0) {
            return response()->json([
                'status'=>false,
                'message'=>'Beberapa mahasiswa sudah memiliki project aktif.',
                'blocked_ids' => $blocked
            ], 422);
        }

        DB::beginTransaction();
        try {
            $project = Project::create([
                'pembimbing_id' => $pembimbing->id,
                'title' => $request->title,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'status' => 'active',
            ]);

            foreach ($request->members as $idx => $mid) {
                ProjectMember::create([
                    'project_id' => $project->id,
                    'mahasiswa_id' => $mid,
                    'is_leader' => ($idx === 0) ? true : false,
                ]);
            }

            DB::commit();

            return response()->json(['status'=>true,'message'=>'Project dibuat','data'=>$project->load('members.mahasiswa')],201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'message'=>'Gagal membuat project','error'=>$e->getMessage()],500);
        }
    }

    // PEMBIMBING — tambahkan anggota
    public function addMember(Request $request, $projectId)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa_data,id'
        ]);

        $pembimbing = $request->user()->pembimbingData;
        $project = Project::findOrFail($projectId);

        if ($project->pembimbing_id !== $pembimbing->id) {
            return response()->json(['status'=>false,'message'=>'Tidak berwenang'],403);
        }

        // cek jika mahasiswa punya active project
        $mid = $request->mahasiswa_id;
        $active = ProjectMember::where('mahasiswa_id',$mid)
            ->whereHas('project', fn($q)=> $q->where('status','active'))
            ->exists();

        if ($active) {
            return response()->json(['status'=>false,'message'=>'Mahasiswa sudah ada project aktif'],422);
        }

        $pm = ProjectMember::create([
            'project_id' => $projectId,
            'mahasiswa_id' => $mid,
            'is_leader' => false
        ]);

        return response()->json(['status'=>true,'message'=>'Anggota ditambahkan','data'=>$pm]);
    }

    // PEMBIMBING — hapus anggota
    public function removeMember(Request $request, $projectId, $memberId)
    {
        $pembimbing = $request->user()->pembimbingData;
        $project = Project::findOrFail($projectId);

        if ($project->pembimbing_id !== $pembimbing->id) {
            return response()->json(['status'=>false,'message'=>'Tidak berwenang'],403);
        }

        $member = ProjectMember::where('project_id',$projectId)->where('id',$memberId)->firstOrFail();
        $member->delete();

        return response()->json(['status'=>true,'message'=>'Anggota dihapus']);
    }

    // PEMBIMBING — ganti anggota (replace mahasiswa A -> B)
    public function replaceMember(Request $request, $projectId, $memberId)
    {
        $request->validate([
            'new_mahasiswa_id' => 'required|exists:mahasiswa_data,id'
        ]);

        $pembimbing = $request->user()->pembimbingData;
        $project = Project::findOrFail($projectId);

        if ($project->pembimbing_id !== $pembimbing->id) {
            return response()->json(['status'=>false,'message'=>'Tidak berwenang'],403);
        }

        $member = ProjectMember::where('project_id',$projectId)->where('id',$memberId)->firstOrFail();

        // cek new mahasiswa tidak punya active project
        $newId = $request->new_mahasiswa_id;
        $active = ProjectMember::where('mahasiswa_id',$newId)
            ->whereHas('project', fn($q)=> $q->where('status','active'))
            ->exists();

        if ($active) {
            return response()->json(['status'=>false,'message'=>'Mahasiswa pengganti sudah punya project aktif'],422);
        }

        $member->update(['mahasiswa_id' => $newId]);

        return response()->json(['status'=>true,'message'=>'Anggota diganti','data'=>$member]);
    }

    // PEMBIMBING — perpanjang / update deadline (boleh)
    public function updateDeadline(Request $request, $projectId)
    {
        $request->validate([
            'deadline' => 'required|date|after:today'
        ]);

        $pembimbing = $request->user()->pembimbingData;
        $project = Project::findOrFail($projectId);

        if ($project->pembimbing_id !== $pembimbing->id) {
            return response()->json(['status'=>false,'message'=>'Tidak berwenang'],403);
        }

        $project->deadline = $request->deadline;
        $project->save();

        return response()->json(['status'=>true,'message'=>'Deadline diperbarui','data'=>$project]);
    }

    // PEMBIMBING — akhiri project lebih cepat (set status completed/cancelled)
    public function endProject(Request $request, $projectId)
    {
        $request->validate([
            'status' => ['required', Rule::in(['completed','cancelled'])],
            'note' => 'nullable|string'
        ]);

        $pembimbing = $request->user()->pembimbingData;
        $project = Project::findOrFail($projectId);

        if ($project->pembimbing_id !== $pembimbing->id) {
            return response()->json(['status'=>false,'message'=>'Tidak berwenang'],403);
        }

        $project->status = $request->status;
        $project->save();

        return response()->json(['status'=>true,'message'=>"Project diubah menjadi {$request->status}",'data'=>$project]);
    }

    // MAHASISWA — list project milik saya
    public function myProjects(Request $request)
    {
        $mhs = $request->user()->mahasiswaData;
        if (!$mhs) return response()->json(['status'=>true,'data'=>[]]);

        $projects = Project::whereHas('members', fn($q)=> $q->where('mahasiswa_id',$mhs->id))
            ->with(['members.mahasiswa.user','pembimbing.user'])
            ->get();

        return response()->json(['status'=>true,'data'=>$projects]);
    }

    // PEMBIMBING — list semua project bimbingannya (optional filter active/completed)
    public function pembimbingProjects(Request $request)
    {
        $pembimbing = $request->user()->pembimbingData;
        $query = Project::with(['members.mahasiswa.user'])->where('pembimbing_id', $pembimbing->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json(['status'=>true,'data'=>$query->get()]);
    }
}
