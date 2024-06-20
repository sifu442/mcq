<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $enrollments = Enrollment::with(['course'])
            ->where('user_id', $user->id)
            ->get();

        return view('dashboard.index', compact('user', 'enrollments'));
    }

    public function exams()
    {
        $user = Auth::user();
        $enrolledCourses = $user->courses;

        $now = Carbon::now();
        $upcomingExams = [];
        $ongoingExams = [];
        $previousExams = [];

        foreach ($enrolledCourses as $course) {
            $enrolledAt = $course->pivot->enrolled_at;
            foreach ($course->exams as $exam) {
                $examStartTime = Carbon::parse($enrolledAt)->addDays($exam->delay_days);
                $examEndTime = $examStartTime->clone()->addHours($exam->available_for_hours);

                if ($now->lt($examStartTime)) {
                    $upcomingExams[] = $exam;
                } elseif ($now->between($examStartTime, $examEndTime)) {
                    $ongoingExams[] = $exam;
                } else {
                    $previousExams[] = $exam;
                }
            }
        }

        return view('dashboard.exams', compact('ongoingExams', 'upcomingExams', 'previousExams'));
    }

}
