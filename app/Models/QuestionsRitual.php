<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionsRitual extends Model
{
    use HasFactory;

    protected $table = 'question';

    protected $fillable = [
        'services',
        'taxi',
        'place',
        'documents',
        'decoration',
        'clothes',
        'material',
        'name',
        'phone'
    ];
}
