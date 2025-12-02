<?php

namespace App\Http\Controllers;

use App\Models\AlumniMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AlumniMemberController extends Controller
{
    public function index()
    {
        $members = AlumniMember::orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }

    public function show($id)
    {
        $member = AlumniMember::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $member
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'graduation_year' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('alumni_photos', 'public');
            $validated['photo_path'] = $path;
        }

        $member = AlumniMember::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Alumni member created successfully',
            'data' => $member
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $member = AlumniMember::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $changedFields = [];

        if ($request->hasFile('photo')) {
            if ($member->photo_path) {
                Storage::disk('public')->delete($member->photo_path);
            }

            $photoPath = $request->file('photo')->store('alumni_photos', 'public');

            if ($member->photo_path !== $photoPath) {
                $member->photo_path = $photoPath;
                $changedFields['photo_path'] = $photoPath;
            }

            unset($validated['photo']);
        }

        foreach ($validated as $field => $newValue) {
            if ($field === 'photo') continue;

            if ($member->$field !== $newValue && !is_null($newValue)) {
                $member->$field = $newValue;
                $changedFields[$field] = $newValue;
            }
        }

        if (!empty($changedFields)) {
            $member->save();
        }

        return response()->json([
            'success' => true,
            'message' => !empty($changedFields) ? 'Alumni member updated successfully' : 'No changes detected',
            'changed_fields' => $changedFields
        ]);
    }


    public function destroy($id)
    {
        $member = AlumniMember::findOrFail($id);

        if ($member->photo_path) {
            Storage::disk('public')->delete($member->photo_path);
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alumni member deleted successfully'
        ]);
    }
}
