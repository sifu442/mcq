<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'time_span', 'price', 'deducted_price', 'published_at', 'featured', 'total_exams', 'resources', 'description'];

    protected $casts = [
        'featured' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($course) {
            if ($course->is_free) {
                $course->price = 0;
            }
        });
    }

    public function scopeFeatured($query)
    {
        $query->where('featured', true);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

}
