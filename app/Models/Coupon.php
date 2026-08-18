<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Coupon extends Model {
    protected $fillable = ['code','description','type','value','min_order','max_discount','usage_limit','used_count','expires_at','is_active'];
    protected function casts(): array { return ['expires_at'=>'date','is_active'=>'boolean','value'=>'decimal:2','min_order'=>'decimal:2']; }
    public function isValid(float $orderTotal): bool { if(!$this->is_active) return false; if($this->expires_at && $this->expires_at->isPast()) return false; if($this->usage_limit && $this->used_count >= $this->usage_limit) return false; if($orderTotal < $this->min_order) return false; return true; }
    public function calculateDiscount(float $orderTotal): float { $discount = $this->type === 'percent' ? ($orderTotal * $this->value / 100) : $this->value; if($this->max_discount) $discount = min($discount, $this->max_discount); return round($discount, 2); }
}
