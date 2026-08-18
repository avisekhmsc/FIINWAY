<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class SellerEarning extends Model {
    use HasFactory;
    protected $fillable = ['seller_id','order_id','order_item_id','order_amount','commission_percent','commission_amount','seller_amount','status','customer_ok_at','hold_until','released_at'];
    protected function casts(): array { return ['customer_ok_at'=>'datetime','hold_until'=>'datetime','released_at'=>'datetime','order_amount'=>'decimal:2','commission_amount'=>'decimal:2','seller_amount'=>'decimal:2']; }
    public function seller() { return $this->belongsTo(User::class,'seller_id'); }
    public function order() { return $this->belongsTo(Order::class); }
    public function orderItem() { return $this->belongsTo(OrderItem::class,'order_item_id'); }
}
