<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanSetup extends Model
{
    use HasFactory;

    protected $fillable = [
        'phases',
        'loan_amount',
        'percentage',
    ];
}
