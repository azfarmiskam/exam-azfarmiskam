<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with(['classroom', 'creator'])
            ->latest()
            ->get()
            ->map(function ($a) {
                $a->is_expired = $a->expires_at->isPast();
                return $a;
            });

        return response()->json(['announcements' => $announcements]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'message' => 'required|string|max:500',
            'duration' => 'required|integer|min:1|max:1440',
            'repeat_interval' => 'nullable|integer|min:0',
        ]);

        $announcement = Announcement::create([
            'classroom_id' => $validated['classroom_id'],
            'message' => $validated['message'],
            'expires_at' => now()->addMinutes($validated['duration']),
            'repeat_interval' => $validated['repeat_interval'] ?? 0,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement sent successfully',
            'announcement' => $announcement->load(['classroom', 'creator']),
        ], 201);
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted',
        ]);
    }

    public function deactivate($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement stopped',
        ]);
    }
}
