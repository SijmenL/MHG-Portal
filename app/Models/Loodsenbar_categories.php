<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class loodsenbar_categories extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_route',
        'c_user_id',
        'u_user_id',
    ];

}