<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'status',
        'courier',
        'shipping_cost',
        'estimated_delivery',
        'note',
        'created_by'
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'estimated_delivery' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Handle status changes automatically
    protected static function boot()
    {
        parent::boot();

        // When saving a shipment
        static::saving(function ($shipment) {
            // If status changed to shipped and it wasn't previously shipped
            if ($shipment->isDirty('status') && $shipment->status === 'shipped' && $shipment->getOriginal('status') !== 'shipped') {
                // Set shipped_at timestamp
                $shipment->shipped_at = now();

                // Update transaction status
                if ($shipment->transaction) {
                    $shipment->transaction->status = 'shipped';
                    $shipment->transaction->save();
                }
            }

            // If status changed to delivered and it wasn't previously delivered
            if ($shipment->isDirty('status') && $shipment->status === 'delivered' && $shipment->getOriginal('status') !== 'delivered') {
                // Set delivered_at timestamp
                $shipment->delivered_at = now();

                // Update transaction status
                if ($shipment->transaction) {
                    $shipment->transaction->status = 'delivered';
                    $shipment->transaction->save();
                }
            }
        });
    }
}
