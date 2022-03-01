<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phases_id',
        'referral_id',
        'sponsor_id',
        'contribution_amount',
        'receive_status',
        'phase_status',
        'group_id',
        'status',
    ];
}
