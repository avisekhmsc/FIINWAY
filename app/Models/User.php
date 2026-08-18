<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'otp', 'otp_expires_at',
        'avatar', 'city', 'state', 'pincode', 'role', 'is_seller',
        'is_active', 'is_blocked', 'referral_code', 'wallet_balance',
        'bank_name', 'account_number', 'ifsc_code', 'upi_id',
        'phone_verified_at',
    ];

    protected $hidden = ['password', 'remember_token', 'otp'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'is_seller' => 'boolean',
            'is_active' => 'boolean',
            'is_blocked' => 'boolean',
            'wallet_balance' => 'decimal:2',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (!$user->referral_code) {
                $user->referral_code = strtoupper(Str::random(8));
            }
        });
    }

    // Relationships
    public function addresses() { return $this->hasMany(UserAddress::class); }
    public function defaultAddress() { return $this->hasOne(UserAddress::class)->where('is_default', true); }
    public function products() { return $this->hasMany(Product::class); }
    public function cart() { return $this->hasOne(Cart::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function sellerOrders() { return $this->hasMany(OrderItem::class, 'seller_id'); }
    public function earnings() { return $this->hasMany(SellerEarning::class, 'seller_id'); }
    public function notifications() { return $this->hasMany(Notification::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function referrals() { return $this->hasMany(Referral::class, 'referrer_id'); }
    public function referralRewards() { return $this->hasMany(ReferralReward::class); }
    public function wishlist() { return $this->hasMany(Wishlist::class); }
    public function wishlists() { return $this->hasMany(Wishlist::class); }   // alias for profile stats
    public function returns() { return $this->hasMany(\App\Models\ReturnRequest::class, 'buyer_id'); }

    public function hasWishlisted(int $productId): bool
    {
        return $this->wishlist()->where('product_id', $productId)->exists();
    }

    // Helpers
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isSeller(): bool { return $this->is_seller; }

    public function totalEarnings(): float
    {
        return $this->earnings()->where('status', 'released')->sum('seller_amount');
    }

    public function pendingEarnings(): float
    {
        return $this->earnings()->whereIn('status', ['pending', 'customer_ok', 'on_hold'])->sum('seller_amount');
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }
}
