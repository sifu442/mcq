<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'course_id', 'duration', 'delay_days', 'available_for_hours', 'syllabus', 'score', 'penalty'];


    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_question')->withTimestamps();
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
