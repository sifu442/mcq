<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'course_id', 'syllabus', 'duration', 'score', 'penalty', 'marks'];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_question')->withTimestamps();
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // public function isOngoingOrUpcoming()
    // {
    //     $now = Carbon::now();
    //     $startDateTime = Carbon::parse($this->start_date);
    //     $endDateTime = Carbon::parse($this->end_date);

    //     // Check if the current date and time is between the start and end date of the exam
    //     return $now->between($startDateTime, $endDateTime);
    // }
}
