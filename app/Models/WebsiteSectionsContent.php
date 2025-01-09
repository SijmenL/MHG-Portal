<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSectionsContent extends Model
{
    protected $table = 'website_sections_content';

    protected $fillable = [
        'section_id',
        'content',
    ];

    public function section()
    {
        return $this->belongsTo(WebsiteSections::class);
    }
}
