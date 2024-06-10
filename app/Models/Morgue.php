<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Morgue extends Model
{
    use HasFactory;

    protected $table = 'morgue';

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
