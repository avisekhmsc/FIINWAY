<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Referral extends Model {
    protected $fillable = ['referrer_id','referred_id','referral_code','eligible_action_done','eligible_at','status'];
    protected function casts(): array { return ['eligible_action_done'=>'boolean','eligible_at'=>'datetime']; }
    public function referrer() { return $this->belongsTo(User::class,'referrer_id'); }
    public function referred() { return $this->belongsTo(User::class,'referred_id'); }
    public function reward()   { return $this->hasOne(ReferralReward::class); }
    public function rewards()  { return $this->hasMany(ReferralReward::class); }
}
