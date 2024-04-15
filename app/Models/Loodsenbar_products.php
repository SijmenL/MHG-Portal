<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class loodsenbar_products extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id', // This is the foreign key
        'image_route',
        'c_user_id',
        'u_user_id',
    ];

    public function category()
    {
        return $this->belongsTo(loodsenbar_categories::class, 'category_id');
    }

}