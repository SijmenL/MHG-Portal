<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsitePage extends Model
{
    protected $table = 'website_pages';


    protected $fillable = [
        'title',
        'slug',
        'custom_route',
        'meta_description',
    ];
}
