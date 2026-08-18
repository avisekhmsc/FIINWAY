<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payout extends Model {
    protected $fillable = ['seller_id','amount','bank_name','account_number','ifsc_code','upi_id','status','transaction_ref','processed_at'];
    protected function casts(): array { return ['processed_at'=>'datetime','amount'=>'decimal:2']; }
    public function seller() { return $this->belongsTo(User::class,'seller_id'); }
}
