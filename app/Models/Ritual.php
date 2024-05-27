<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use willvincent\Rateable\Rateable;

class Ritual extends Model
{
    use HasFactory, Rateable;

    protected $table = 'rituals';


    protected $fillable = [
        'company_ritual',
        'phone_ritual',
        'description_ritual',
        'address_ritual',
        'work_time_ritual',
        'service_ritual',
        'site_ritual',
        'social_network_ritual',
        'files',
        'paths',
        'prices'
    ];

    public function files()
    {
        return $this->hasMany(RitualFile::class);
    }
}


