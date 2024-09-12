<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        /* 'files',
        'paths', */
        'seo_title',
        'seo_description',
        'seo_keywords'
    ];
}
