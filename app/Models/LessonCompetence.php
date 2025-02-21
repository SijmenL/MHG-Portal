<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonCompetence extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'lesson_id',
        'description',
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
    public function competenceResults()
    {
        return $this->hasMany(UserLessonCompetenceResult::class, 'competence_id');
    }
}
