<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * ADMIN — Buat event baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai'
        ]);

        $event = Event::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Event berhasil dibuat.',
            'data' => $event
        ], 201);
    }

    /**
     * ADMIN — Edit event
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai'
        ]);

        $event->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Event diperbarui.',
            'data' => $event
        ]);
    }

    /**
     * ADMIN — Hapus event
     */
    public function destroy($id)
    {
        Event::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Event berhasil dihapus.'
        ]);
    }

    /**
     * PUBLIC / ALL ROLES — Lihat event (expired > 7 hari otomatis disembunyikan)
     */
    public function index()
    {
        $today = Carbon::today();

        // event expired lewat 7 hari → auto hide
        $events = Event::all()
            ->filter(function ($event) use ($today) {
                $end = Carbon::parse($event->tanggal_selesai ?? $event->tanggal_mulai);
                return $end->copy()->addDays(7)->greaterThanOrEqualTo($today);
            })
            ->values();

        return response()->json([
            'status' => true,
            'data' => $events
        ]);
    }
}
