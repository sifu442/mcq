<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $enrolledCourses = auth()->user()->courses;

        return view('dashboard.dashboard', compact('enrolledCourses'));
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
