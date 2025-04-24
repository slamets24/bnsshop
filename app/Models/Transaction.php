<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_code',
        'tracking_token',
        'total',
        'status',
        'note',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function shippingAddress()
    {
        return $this->hasOne(ShippingAddress::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
}
