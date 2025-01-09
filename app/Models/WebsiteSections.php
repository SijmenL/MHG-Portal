<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSections extends Model
{
    protected $table = 'website_page_sections';

    protected $fillable = [
        'page_id',
        'page_position',
        'content_type',
    ];

    public function page()
    {
        return $this->belongsTo(WebsitePage::class);
    }
}
