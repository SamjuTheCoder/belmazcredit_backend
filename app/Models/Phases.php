<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phases extends Model
{
    use HasFactory;

    protected $fillable = [
        'phases',
        'user_number',
        'receive_status'
    ];


}
