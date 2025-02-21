<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLessonCompetenceResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_id',
        'passed',
    ];

    /**
     * Define a relationship to the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define a relationship to the test.
     */
    public function competence()
    {
        return $this->belongsTo(LessonCompetence::class, 'competence_id');
    }
}
