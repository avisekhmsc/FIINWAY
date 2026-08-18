<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ReferralReward extends Model {
    protected $fillable = ['user_id','referral_id','amount','status','credited_at'];
    protected function casts(): array { return ['credited_at'=>'datetime','amount'=>'decimal:2']; }
    public function user() { return $this->belongsTo(User::class); }
    public function referral() { return $this->belongsTo(Referral::class); }
}
