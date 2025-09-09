<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class File extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'type',
        'access',
        'folder_id',
        'fileable_id',
        'fileable_type',
        'user_id'
    ];

    public function fileable()
    {
        return $this->morphTo();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function parent()
    {
        return $this->belongsTo(File::class, 'folder_id');
    }
    public function children()
    {
        return $this->hasMany(File::class, 'folder_id');
    }

    public function checkAccess($user = null, $action = 'view')
    {
        $user ??= Auth::user();

        // 1. File-level access
        if ($this->access === 'teachers' && !$user->isTeacher()) {
            return false;
        }

        // 2. Based on fileable type
        if ($this->fileable_type === 'App\\Models\\Lesson') {
            $lesson = $this->fileable;

            if ($action === 'edit') {
                // Only teachers can modify files in a lesson
                return $user->isTeacher() && method_exists($lesson, 'checkLessonAccess')
                    ? $lesson->checkLessonAccess($user->id)
                    : false;
            }

            // 'view' mode
            return method_exists($lesson, 'checkLessonAccess')
                ? $lesson->checkLessonAccess($user->id)
                : true;
        }

        // 3. Other fileable types...
        // You can add additional rules for edit/view

        return true;
    }

}
