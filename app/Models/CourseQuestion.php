<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    // Kebalikan
    public function course(){
        return $this->belongsTo(Course::class, 'course_id');
    }

     public function answers(){
        return $this->hasMany(CourseAnswer::class, 'course_question_id', 'id');
    }

    public function correctAnswer()
{
    return $this->hasOne(CourseAnswer::class, 'course_question_id', 'id')->where('is_correct', true);
}
}
