<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\OJTInformation;
use App\Helpers\AuditLogger;

class OJTController extends Controller
{
    public function showForm()
    {
        return view('ojtCoordinator.report_form');
    }

    public function generateReport(Request $request)
    {
        // Get the current date and subtract 6 months
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $selectedCourse = $request->input('course');

        $students = User::where('role', 0)
            ->where('status', 1)
            ->where('course', $selectedCourse)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->get();
        $studentData = [];

        foreach ($students as $student) {
            $ojt = OJTInformation::where('studentNum', $student->studentNum)->first();

            $studentData[] = [
                'student' => $student,
                'ojt' => $ojt,
            ];
        }

        return view('ojtCoordinator.report_form', compact('studentData'));
    }
}
