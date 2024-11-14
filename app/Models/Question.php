<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'subject_id', 'exam_id', 'last_appeared', 'previous_exam', 'post', 'date', 'explanation', 'topic', 'option_a', 'option_b', 'option_c', 'option_d'];

    protected $casts = ['options' => 'json'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_question')->withTimestamps();
    }

}
