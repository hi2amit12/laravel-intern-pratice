<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index1(Request $request)
    {
        // Get search query
        $search = $request->input('search');

        // Get per page limit
        $perPage = $request->input('per_page', 10); // default 10

        // Build query
        $query = Student::with(['classroom', 'marks.subject']);

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhereHas('classroom', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('marks.subject', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
        }

        // Paginate results
        $students = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'students' => $students
        ]);
    }

    public function index()
    {
        // Fetch all students with their class and marks (including subjects)
        $students = Student::with(['classroom', 'marks.subject'])->get();

        // Transform data into desired JSON format
        $formatted = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'age' => $student->age,
                'class' => $student->classroom->name ?? null,
                'roll_no' => $student->roll_no ?? 'N/A',
                'email' => $student->email ?? null,
                'marks' => $student->marks->mapWithKeys(function ($mark) {
                    return [$mark->subject->name => $mark->marks];
                }),
            ];
        });

        return response()->json([
            'status' => 'success',
            'students' => $formatted
        ]);
    }

    public function show($id)
    {
        $student = Student::with(['classroom', 'marks.subject'])->find($id);

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'student' => $student
        ]);
    }
}
