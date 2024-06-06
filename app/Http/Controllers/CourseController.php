<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function show(Course $course)
    {
        $subjects = collect();
        foreach ($course->exams as $exam) {
            foreach ($exam->questions as $question) {
                $subjects->push($question->subject);
            }
        }
        $uniqueSubjects = $subjects->unique();

        $exams = $course->exams;

        $currentDate = Carbon::now();
        $examDate = $currentDate->addDays(4);
        $formattedExamDate = $examDate->format('d/m/Y');
        $dayOfWeek = $examDate->format('l');

        return view('course.show', [
            'course' => $course,
            'subjects' => $uniqueSubjects,
            'exams' => $exams,
            'examDate' => $formattedExamDate,
            'dayOfWeek' => $dayOfWeek,
        ]);
    }
    public function buy($courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = auth()->user();

        return view('course.purchase', compact('course', 'user'));
    }

    public function purchase(Request $request, $courseId)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'phone_number' => 'required|string|max:15',
        ]);

        // Process the purchase...

        return redirect()->route('home')->with('success', 'Purchase successful!');
    }
}
