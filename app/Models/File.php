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
        'location',
        'location_id',
        'share_permission',
        'share_hash',
        'location_id',
        'user_id'
    ];

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

}
