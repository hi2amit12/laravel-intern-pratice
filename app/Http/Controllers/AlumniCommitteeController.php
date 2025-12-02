<?php

namespace App\Http\Controllers;

use App\Models\AlumniCommittee;
use App\Models\AlumniMember;
use Illuminate\Http\Request;

class AlumniCommitteeController extends Controller
{
    private static $positionOrder = [
        'Ex-Officio Chairperson' => 1,
        'President' => 2,
        'Vice President' => 3,
        'Secretary' => 4,
        'Treasurer' => 5,
        'Ex-President' => 6,
        'Joint Secretary' => 7,
        'Executive Member' => 8,
        'Advisory Member' => 9,
        'Coordinator' => 10,
    ];

    public function index()
    {
        $committees = AlumniCommittee::with('alumniMember')->get();

        $sortedCommittees = $committees->sortBy(function ($committee) {
            $positionOrder = self::$positionOrder[$committee->position] ?? 999;
            return [$positionOrder, $committee->display_order];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $sortedCommittees
        ]);
    }

    public function show($id)
    {
        $committee = AlumniCommittee::with('alumniMember')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $committee
        ]);
    }

    public function getPositions()
    {
        $positions = array_keys(self::$positionOrder);

        return response()->json([
            'success' => true,
            'data' => $positions
        ]);
    }

    public function getAvailableMembers()
    {
        $members = AlumniMember::whereDoesntHave('committee')
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
            'alumni_member_id' => 'required|exists:alumni_members,id|unique:alumni_committee,alumni_member_id',
            'position' => 'required|string|max:255',
            'display_order' => 'nullable|integer'
        ]);

        if (!isset($validated['display_order'])) {
            $maxDisplayOrder = AlumniCommittee::where('position', $validated['position'])
                ->max('display_order') ?? 0;
            $validated['display_order'] = $maxDisplayOrder + 1;
        }

        $committee = AlumniCommittee::create($validated);
        $committee->load('alumniMember');

        return response()->json([
            'success' => true,
            'message' => 'Committee member added successfully',
            'data' => $committee
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $committee = AlumniCommittee::findOrFail($id);

        $validated = $request->validate([
            'alumni_member_id' => 'sometimes|exists:alumni_members,id|unique:alumni_committee,alumni_member_id,' . $id,
            'position' => 'sometimes|string|max:255',
            'display_order' => 'nullable|integer'
        ]);

        $committee->update($validated);
        $committee->load('alumniMember');

        return response()->json([
            'success' => true,
            'message' => 'Committee member updated successfully',
            'data' => $committee
        ]);
    }

    public function destroy($id)
    {
        $committee = AlumniCommittee::findOrFail($id);
        $committee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Committee member removed successfully'
        ]);
    }
}
