<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Exam;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $enrollments = Enrollment::with(['course'])
            ->where('user_id', $user->id)
            ->get();

        // Load courses related to the user
        $courses = Course::whereIn('id', $enrollments->pluck('course_id'))->get();

        return view('dashboard.index', compact('user', 'enrollments', 'courses'));
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $courseId = $request->input('course');

        // Retrieve exams related to the logged-in user's enrollments
        $enrollmentsQuery = Enrollment::with(['course', 'course.exams'])
            ->where('user_id', $user->id);

        if ($courseId) {
            $enrollmentsQuery->where('course_id', $courseId);
        }

        $enrollments = $enrollmentsQuery->get();

        $ongoingExams = [];
        $upcomingExams = [];
        $previousExams = [];

        foreach ($enrollments as $enrollment) {
            $routine = json_decode($enrollment->routine, true);
            if ($routine) {
                foreach ($routine as $examRoutine) {
                    $exam = Exam::find($examRoutine['exam_id']);
                    if ($exam) {
                        $startTime = Carbon::parse($examRoutine['start_time']);
                        $endTime = Carbon::parse($examRoutine['end_time']);

                        if ($now->between($startTime, $endTime)) {
                            $ongoingExams[] = (object) [
                                'id' => $exam->id,
                                'name' => $exam->name,
                                'duration' => $exam->duration,
                                'end_date' => $endTime->format('Y-m-d H:i:s')
                            ];
                        } elseif ($now->lt($startTime)) {
                            $upcomingExams[] = (object) [
                                'id' => $exam->id,
                                'name' => $exam->name,
                                'duration' => $exam->duration,
                                'start_date' => $startTime->format('Y-m-d H:i:s')
                            ];
                        } else {
                            $previousExams[] = (object) [
                                'id' => $exam->id,
                                'name' => $exam->name,
                                'duration' => $exam->duration,
                                'end_date' => $endTime->format('Y-m-d H:i:s')
                            ];
                        }
                    }
                }
            }
        }

        return view('dashboard.exams', compact('ongoingExams', 'upcomingExams', 'previousExams'));
    }
}
