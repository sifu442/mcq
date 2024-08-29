<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Exam;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ExamResponse;
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

    public function show($slug)
{
    $course = Course::where('slug', $slug)->firstOrFail();
    $user = Auth::user();
    $now = Carbon::now();

    // Fetch enrollments including course and exams relationships
    $enrollments = Enrollment::with(['course', 'course.exams'])
        ->where('user_id', $user->id)
        ->get();

    $ongoingExams = [];
    $upcomingExams = [];
    $previousExams = [];

    foreach ($enrollments as $enrollment) {
        $routine = $enrollment->routine;

        if (is_string($routine)) {
            $routine = json_decode($routine, true);
        }

        if (is_array($routine)) {
            $startsFrom = $enrollment->starts_from; // Retrieve the starts_from exam ID

            // Filter exams based on starts_from
            $examsToShow = [];
            if ($startsFrom) {
                // Only show exams that come after the specified exam ID within the course
                $examIdsFromStartsFrom = Course::find($course->id)
                    ->exams()
                    ->where('id', '>=', $startsFrom) // Ensure you filter correctly based on ID
                    ->pluck('id')
                    ->toArray();

                foreach ($routine as $examRoutine) {
                    if (in_array($examRoutine['exam_id'], $examIdsFromStartsFrom)) {
                        $examsToShow[] = $examRoutine;
                    }
                }
            } else {
                // If starts_from is not set, list all exams under the course
                $examsToShow = $routine;
            }

            foreach ($examsToShow as $examRoutine) {
                $exam = Exam::find($examRoutine['exam_id']);
                if ($exam) {
                    $startTime = Carbon::parse($examRoutine['start_time']);
                    $endTime = Carbon::parse($examRoutine['end_time']);

                    $examResponse = ExamResponse::where('user_id', $user->id)
                        ->where('exam_id', $exam->id)
                        ->first();

                    if ($examResponse) {
                        $previousExams[] = (object) [
                            'id' => $exam->id,
                            'name' => $exam->name,
                            'duration' => $exam->duration,
                            'end_date' => $endTime->format('Y-m-d H:i:s'),
                            'total_score' => $examResponse->total_score
                        ];
                    } elseif ($now->between($startTime, $endTime)) {
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
