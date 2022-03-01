<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase3 extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_id',
        'sponsor_id',
        'amount',
    ];
}
