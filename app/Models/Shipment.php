<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Shipment extends Model {
    protected $fillable = ['order_id','seller_id','courier_name','tracking_id','tracking_url','status','expected_delivery'];
    protected function casts(): array { return ['expected_delivery'=>'date']; }
    public function order() { return $this->belongsTo(Order::class); }
    public function seller() { return $this->belongsTo(User::class,'seller_id'); }
    public function events() { return $this->hasMany(TrackingEvent::class)->orderByDesc('event_at'); }
    // Items in this shipment = all order items belonging to the same seller
    public function items() {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id')
                    ->when($this->seller_id, fn($q) => $q->whereHas('product', fn($p) => $p->where('user_id', $this->seller_id)));
    }
}

