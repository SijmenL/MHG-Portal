<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'lesson_id',
        'date',
        'max_points',
    ];

    /**
     * Define a relationship to the lesson.
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Define a relationship to the user test results.
     */
    public function testResults()
    {
        return $this->hasMany(UserLessonTestResult::class, 'test_id');
    }
}
