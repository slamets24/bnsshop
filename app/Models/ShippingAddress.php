<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'recipient_name',
        'phone_number',
        'email',
        'address',
        'province',
        'city',
        'district',
        'postal_code',
        'note',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
