<?php

namespace App\Http\Controllers;

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

    return view('course.show', ['course' => $course, 'subjects' => $uniqueSubjects, 'exams' => $exams]);
}



}
