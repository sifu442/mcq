<?php

// Create middleware using artisan command
// php artisan make:middleware EnsureExamIsAccessible

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\Exam;
use App\Models\ExamResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureExamIsAccessible
{
    public function handle(Request $request, Closure $next)
    {
        $exam = Exam::find($request->examId);
        $user = Auth::user();

        if (!$exam) {
            abort(404, 'Exam not found');
        }

        // Check if the user has already submitted the exam
        $examResponse = ExamResponse::where('user_id', $user->id)->where('exam_id', $request->examId)->first();
        if ($examResponse) {
            abort(403, 'You have already submitted this exam.');
        }

        // Check if the exam is ongoing or upcoming
        $now = Carbon::now();
        $enrolledAt = $user->courses()->where('course_id', $exam->course_id)->first()->pivot->enrolled_at;

        $examStartTime = Carbon::parse($enrolledAt)->addDays($exam->delay_days);
        $examEndTime = $examStartTime->clone()->addHours($exam->available_for_hours);

        if ($now->lt($examStartTime)) {
            abort(403, 'This exam is not yet available.');
        } elseif ($now->gt($examEndTime)) {
            abort(403, 'This exam is no longer available.');
        }

        return $next($request);
    }
}

