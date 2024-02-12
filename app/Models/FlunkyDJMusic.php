<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlunkyDJMusic extends Model
{
    protected $table = 'flunkydj_music';
    protected $fillable = ['display_title', 'music_title', 'image', 'music_file', 'music_owner', 'fade_in', 'fade_out', 'play_type'];
}
