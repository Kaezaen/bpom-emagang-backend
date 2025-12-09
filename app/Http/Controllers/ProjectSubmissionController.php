<?php

namespace App\Http\Controllers;

use App\Models\ProjectSubmission;
use App\Models\ProjectMember;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectSubmissionController extends Controller
{
    // MAHASISWA — upload submission
    public function store(Request $request, $projectId)
    {
        $mhs = $request->user()->mahasiswaData;

        $request->validate([
            'file' => 'required|mimes:zip,rar,pdf,doc,docx|max:15000',
            'catatan' => 'nullable|string'
        ]);

        $project = Project::findOrFail($projectId);

        // cek apakah mahasiswa adalah anggota project
        $isMember = $project->members()->where('mahasiswa_id', $mhs->id)->exists();

        if (!$isMember) {
            return response()->json(['status'=>false,'message'=>'Anda bukan anggota project ini'],403);
        }

        // upload file
        $path = $request->file('file')->store('project-submission', 'public');

        $submission = ProjectSubmission::create([
            'project_id' => $projectId,
            'mahasiswa_id' => $mhs->id,
            'file_path' => $path,
            'catatan' => $request->catatan,
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Submission berhasil dikirim.',
            'data' => $submission
        ], 201);
    }

    // PEMBIMBING — list submissions
   public function listByProject(Request $request, $projectId)
    {
        $pembimbing = $request->user()->pembimbingData;

        $project = Project::where('id', $projectId)
            ->where('pembimbing_id', $pembimbing->id)
            ->firstOrFail();

        $submissions = ProjectSubmission::with('mahasiswa.user')
            ->where('project_id', $projectId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $submissions
        ]);
    }

    public function approve(Request $request, $submissionId)
    {
        $pembimbing = $request->user()->pembimbingData;

        $submission = ProjectSubmission::with('project')
            ->findOrFail($submissionId);

        if ($submission->project->pembimbing_id !== $pembimbing->id) {
            return response()->json(['status'=>false,'message'=>'Tidak berwenang'],403);
        }

        $submission->update([
            'status' => 'verified',
            'catatan' => $request->catatan ?? null,
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Submission disetujui'
        ]);
    }

    public function requestRevision(Request $request, $submissionId)
    {
        $request->validate([
            'catatan' => 'required|string|min:5'
        ]);

        $pembimbing = $request->user()->pembimbingData;

        $submission = ProjectSubmission::with('project')
            ->findOrFail($submissionId);

        if ($submission->project->pembimbing_id !== $pembimbing->id) {
            return response()->json(['status'=>false,'message'=>'Tidak berwenang'],403);
        }

        $submission->update([
            'status' => 'rejected',
            'catatan' => $request->catatan
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Revision requested'
        ]);
    }
}