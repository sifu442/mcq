<?php

namespace App\Http\Controllers;


use App\Models\Course;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function exams($courseSlug)
{
    
    // Alternatively, if you're using course ID
    $course = Course::where('slug', $courseSlug)->with('exams')->firstOrFail();

    // Pass the course and its exams to the view
    return view('course.exams', compact('course'));
}
}

