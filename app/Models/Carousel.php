<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carousel extends Model
{
    use HasFactory;

    protected $fillable = ['image_path', 'caption'];

    public function getImageUrlAttribute()
    {
        return Storage::url($this->image_path);
    }
}
