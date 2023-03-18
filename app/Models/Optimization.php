<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Optimization extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'amount',
        'optimization_type',#crb or hustler
        'method',#share or mpesa
        'status',
        'reference_no',
        'mpesa_code',
        'isDismiss',
        'date_approved',
        'isApproved',
        'phone_number',
        'message'
    ];
}
