<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    protected $fillable = [
        'content',
        'user_id',
        'post_id',
        'comment_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'post_id')->where('location', 1);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'comment_id');
    }
}
