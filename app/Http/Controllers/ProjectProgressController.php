<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectProgressController extends Controller
{
    /**
     * MAHASISWA — Upload progress
     */
    public function store(Request $request, $projectId)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'required|mimes:zip,pdf,doc,docx|max:10000'
        ]);

        $mhs = $request->user()->mahasiswaData;

        $project = Project::whereHas('members', function ($q) use ($mhs) {
            $q->where('mahasiswa_id', $mhs->id);
        })->findOrFail($projectId);

        // Upload file
        $path = $request->file('file')->store("project-progress", "public");

        $progress = ProjectProgress::create([
            'project_id' => $projectId,
            'mahasiswa_id' => $mhs->id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_path' => $path ?? null
        ]);


        return response()->json([
            'status' => true,
            'message' => 'Progress berhasil di-upload.',
            'data' => $progress
        ]);
    }


    /**
     * MAHASISWA — Lihat progress milik sendiri
     */
    public function index($projectId, Request $request)
    {
        $mhs = $request->user()->mahasiswaData;

        $progress = ProjectProgress::where('project_id', $projectId)
            ->where('mahasiswa_id', $mhs->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $progress
        ]);
    }

    /**
     * PEMBIMBING — Lihat semua progress mahasiswa pada project
     */
    public function indexForPembimbing($projectId, Request $request)
    {
        $pembimbing = $request->user()->pembimbingData;

        $project = Project::findOrFail($projectId);

        if ($project->pembimbing_id !== $pembimbing->id) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak berwenang.'
            ], 403);
        }

        $progress = ProjectProgress::with('mahasiswa.user')
            ->where('project_id', $projectId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $progress
        ]);
    }

    /**
     * PEMBIMBING — ACC / Reject progress
     */
    public function review(Request $request, $progressId)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan' => 'nullable|string'
        ]);

        $progress = ProjectProgress::with('project')->findOrFail($progressId);

        $pembimbing = $request->user()->pembimbingData;

        // Pastikan pembimbing yang sama
        if ($progress->project->pembimbing_id !== $pembimbing->id) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak berwenang.'
            ], 403);
        }

        $progress->update([
            'status' => $request->status,
            'catatan' => $request->catatan
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Review progress berhasil.',
            'data' => $progress
        ]);
    }
}
