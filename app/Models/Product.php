<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Product extends Model {
    use SoftDeletes, HasFactory;
    protected $fillable = ['user_id','category_id','name','slug','description','condition_type','condition_label','selling_price','original_price','discount_percent','stock','brand','delivery_type','delivery_days','pickup_available','city','state','pincode','product_age_months','bill_available','warranty_available','warranty_info','damage_details','status','reject_reason','rating','rating_count','view_count'];
    protected function casts(): array { return ['bill_available'=>'boolean','warranty_available'=>'boolean','pickup_available'=>'boolean','selling_price'=>'decimal:2','original_price'=>'decimal:2','discount_percent'=>'decimal:2']; }
    public function seller() { return $this->belongsTo(User::class,'user_id'); }
    public function category() { return $this->belongsTo(Category::class); }
    public function images() { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function primaryImage() { return $this->hasOne(ProductImage::class)->where('is_primary',true); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function cartItems() { return $this->hasMany(CartItem::class); }
    public function getPrimaryImageUrlAttribute() {
        $img = $this->images()->where('is_primary', true)->first() ?? $this->images()->first();
        if (!$img) return asset('images/placeholder.png');
        $path = $img->image_path;
        // If it's already a full CDN/external URL, use it directly
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return asset('storage/' . ltrim($path, '/'));
    }
    public function getDiscountAmountAttribute() { return $this->original_price ? ($this->original_price - $this->selling_price) : 0; }
}
