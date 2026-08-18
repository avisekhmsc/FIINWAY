<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    protected $table = 'returns';

    protected $fillable = ['order_id', 'user_id', 'reason', 'description', 'status', 'admin_note'];

    public function order()  { return $this->belongsTo(Order::class); }
    public function buyer()  { return $this->belongsTo(User::class, 'user_id'); }
    public function refund() { return $this->hasOne(Refund::class, 'order_id', 'order_id'); }

    public function statusLabel(): string
    {
        return match($this->status) {
            'requested'  => 'Requested',
            'approved'   => 'Approved',
            'rejected'   => 'Rejected',
            'processing' => 'Processing',
            'completed'  => 'Completed',
            default      => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'approved', 'completed' => 'success',
            'rejected'              => 'danger',
            'processing'            => 'warning',
            default                 => 'primary',
        };
    }
}
