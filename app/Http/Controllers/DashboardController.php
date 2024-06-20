<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Exam;
use App\Models\Enrollment;
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

    public function show()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Retrieve exams related to the logged-in user's enrollments
        $enrollments = Enrollment::with(['course', 'course.exams'])
            ->where('user_id', $user->id)
            ->get();

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

        return view('exams', compact('ongoingExams', 'upcomingExams', 'previousExams'));
    }

}
