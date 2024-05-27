<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RitualFile extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'path', 'ritual_id'];

    

    public function ritual()
    {
        return $this->belongsTo(Ritual::class);
    }
}
