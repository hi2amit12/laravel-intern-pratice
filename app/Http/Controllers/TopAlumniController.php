<?php

namespace App\Http\Controllers;

use App\Models\AlumniMember;
use App\Models\TopAlumni;
use Illuminate\Http\Request;

class TopAlumniController extends Controller
{
    public function index()
    {
        $topAlumni = TopAlumni::with('alumniMember')
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topAlumni
        ]);
    }

    public function show($id)
    {
        $topAlumni = TopAlumni::with('alumniMember')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $topAlumni
        ]);
    }

    public function getAvailableMembers()
    {
        $members = AlumniMember::whereDoesntHave('topAlumni')
            ->select('id', 'name', 'department', 'graduation_year', 'job_title', 'photo_path')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }

    public function getAllMembers()
    {
        $members = AlumniMember::select('id', 'name', 'department', 'graduation_year', 'job_title', 'photo_path')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alumni_member_id' => 'required|exists:alumni_members,id|unique:top_alumni,alumni_member_id',
            'achievement' => 'nullable|string',
            'display_order' => 'nullable|integer'
        ]);

        $topAlumni = TopAlumni::create($validated);
        $topAlumni->load('alumniMember');

        return response()->json([
            'success' => true,
            'message' => 'Top alumni added successfully',
            'data' => $topAlumni
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $topAlumni = TopAlumni::findOrFail($id);

        $validated = $request->validate([
            'alumni_member_id' => 'sometimes|exists:alumni_members,id|unique:top_alumni,alumni_member_id,' . $id,
            'achievement' => 'nullable|string',
            'display_order' => 'nullable|integer'
        ]);

        $topAlumni->update($validated);
        $topAlumni->load('alumniMember');

        return response()->json([
            'success' => true,
            'message' => 'Top alumni updated successfully',
            'data' => $topAlumni
        ]);
    }

    public function destroy($id)
    {
        $topAlumni = TopAlumni::findOrFail($id);
        $topAlumni->delete();

        return response()->json([
            'success' => true,
            'message' => 'Top alumni removed successfully'
        ]);
    }
}
