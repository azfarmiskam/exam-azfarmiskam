<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassroomGroup;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomGroupController extends Controller
{
    public function index(Classroom $classroom)
    {
        $groups = $classroom->groups()->withCount('students')->get();
        
        return response()->json([
            'groups' => $groups
        ]);
    }

    public function store(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group = $classroom->groups()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Group created successfully',
            'group' => $group
        ], 201);
    }

    public function update(Request $request, Classroom $classroom, ClassroomGroup $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Group updated successfully',
            'group' => $group
        ]);
    }

    public function destroy(Classroom $classroom, ClassroomGroup $group)
    {
        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Group deleted successfully'
        ]);
    }
}
