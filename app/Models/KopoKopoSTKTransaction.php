<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KopoKopoSTKTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount',
        'reference',
        'trans_id',
        'status',
        'senderPhoneNumber',
        'tillNumber',
        'senderFirstName',
        'senderLastName',
        'request_reference',
        'customer_id',
        'result_desc'
    ];
}

