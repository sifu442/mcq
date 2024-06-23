<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'time_span', 'price', 'discounted_price', 'featured', 'total_exams', 'resources', 'description'];

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

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'enrollments')->withPivot('enrolled_at');
    }
}
