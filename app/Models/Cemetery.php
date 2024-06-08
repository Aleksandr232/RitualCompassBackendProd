<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cemetery extends Model
{
    use HasFactory;

    protected $table = 'cemetery';

    protected $fillable = [
        'title',
        'description',
        'slug',
        'files',
        'paths',
        'coordinates',
        'seo_title',
        'seo_description',
        'seo_keywords'
    ];
}
