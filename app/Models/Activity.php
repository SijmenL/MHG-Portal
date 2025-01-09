<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'content',
        'public',
        'user_id',
        'date_start',
        'date_end',
        'image',
        'title',
        'roles',
        'users',
        'price',
        'location',
        'organisator',
        'repeat',
        'presence',
        'lesson_id',
    ];

    protected $casts = [
        'date_start' => 'datetime',
        'date_end' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }


    public function formElements()
    {
        return $this->hasMany(ActivityFormElement::class);
    }

    public function activityFormElements()
    {
        return $this->hasMany(ActivityFormElement::class, 'activity_id');
    }

}
