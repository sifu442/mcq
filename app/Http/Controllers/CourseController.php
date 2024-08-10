<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Course;
use App\Models\Purchase;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CourseController extends Controller
{
    private function courseinfo(Course $course)
    {
        $subjects = collect();
        foreach ($course->exams as $exam) {
            foreach ($exam->questions as $question) {
                $subjects->push($question->subject);
            }
        }
        $uniqueSubjects = $subjects->unique();

        // Calculate exam dates
        $currentDate = Carbon::now();
        $examInfo = [];
        $delayDays = 3; // Initial delay days
        foreach ($course->exams as $exam) {
            $examInfo[] = [
                'examName' => $exam->name,
                'date' => $currentDate->addDays($delayDays)->format('d/m/Y'),
                'dayOfWeek' => $currentDate->format('l'),
            ];
            $delayDays += $exam->delays_date + $exam->available_for_hours / 24;
        }

        return [
            'course' => $course,
            'subjects' => $uniqueSubjects,
            'examInfo' => $examInfo,
        ];
    }

    // Show the course with routine data
    public function show(Course $course)
    {
        $data = $this->courseinfo($course);

        return view('course.show', $data);
    }

    // Generate and download the routine as PDF
    public function downloadRoutine(Course $course)
    {
        $data = $this->courseinfo($course);

        $pdf = Pdf::loadView('course.routine_pdf', $data)
            ->setOption([
                'fontDir' => public_path('/fonts'),
                'fontCache' => public_path('/fonts'),
                'defaultFont' => 'Noto Sans Bengali'
        ]);

        $fileName = Str::slug($course->title) . '-routine.pdf';

        return $pdf->stream($fileName);
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
            'phone_number' => 'required|string|max:15|regex:/^\d{11}$/',
        ]);

        $course = Course::findOrFail($courseId);

        // Create a new purchase record
        Purchase::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'payment_method' => $request->input('payment_method'),
            'phone_number' => $request->input('phone_number'),
            'amount' => $course->price,
        ]);

        return redirect()->route('home')->with('success', 'Purchase successful!');
    }
}
