<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Order extends Model {
    use HasFactory;
    protected $fillable = ['order_number','user_id','address_id','subtotal','delivery_charge','discount','total','coupon_code','delivery_option','status','payment_status','payment_method','transaction_id','paid_at','delivered_at','customer_confirmed','customer_confirmed_at'];
    protected function casts(): array { return ['paid_at'=>'datetime','delivered_at'=>'datetime','customer_confirmed'=>'boolean','customer_confirmed_at'=>'datetime','subtotal'=>'decimal:2','total'=>'decimal:2']; }
    public function buyer() { return $this->belongsTo(User::class,'user_id'); }
    public function address() { return $this->belongsTo(UserAddress::class,'address_id'); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function payment() { return $this->hasOne(Payment::class); }
    public function shipments() { return $this->hasMany(Shipment::class); }
    public function earnings() { return $this->hasMany(SellerEarning::class); }
    public function statusLabel(): string { return match($this->status) { 'pending'=>'Order Placed','confirmed'=>'Seller Confirmed','packed'=>'Packed','shipped'=>'Shipped','out_for_delivery'=>'Out for Delivery','delivered'=>'Delivered','cancelled'=>'Cancelled','returned'=>'Returned',default=>ucfirst($this->status) }; }
    public function statusStep(): int { return match($this->status) { 'pending'=>1,'confirmed'=>2,'packed'=>3,'shipped'=>4,'out_for_delivery'=>5,'delivered'=>6,default=>0 }; }
}
