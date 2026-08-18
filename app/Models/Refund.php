<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_id',
        'gateway_refund_id',
        'amount',
        'reason',
        'status',
        'transaction_ref',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function order()   { return $this->belongsTo(Order::class); }
    public function payment() { return $this->belongsTo(Payment::class); }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'    => 'Refund Requested',
            'processed'  => 'Refund Processed',
            'failed'     => 'Refund Failed',
            default      => ucfirst($this->status),
        };
    }
}
