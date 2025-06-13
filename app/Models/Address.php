<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'barangay',
        'municipality',
        'province',
        'zip_code',
    ];

}
