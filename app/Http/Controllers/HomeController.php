<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        $courses = Course::all();

        foreach ($courses as $course) {
            $course->price = $this->convertToBengaliNumber($course->price);
            $course->discounted_price = $this->convertToBengaliNumber($course->discounted_price);
            $course->total_exams = $this->convertToBengaliNumber($course->total_exams);
            $course->time_span = $this->formatTimeSpan($course->time_span);
        }

        return view('home', compact('courses'));
    }

    private function convertToBengaliNumber($number)
    {
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bengaliNumbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

        return str_replace($englishNumbers, $bengaliNumbers, $number);
    }

    private function formatTimeSpan($days)
    {
        $months = intdiv($days, 30); // Calculate full months
        $remainingDays = $days % 30; // Calculate remaining days

        $monthsText = $months > 0 ? $this->convertToBengaliNumber($months) . ' মাস ' : '';
        $daysText = $remainingDays > 0 ? $this->convertToBengaliNumber($remainingDays) . ' দিন' : '';

        // Combine months and days, remove extra space if any
        return trim($monthsText . $daysText);
    }
}
