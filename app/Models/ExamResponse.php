<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamResponse extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'exam_id', 'response_data', 'total_score', 'correct_count', 'wrong_count', 'unanswered_count', 'lost_points'];

    protected $casts = [
        'response_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
