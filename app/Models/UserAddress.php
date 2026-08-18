<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserAddress extends Model {
    protected $fillable = ['user_id','label','full_name','phone','address_line1','address_line2','city','state','pincode','is_default'];
    public function user() { return $this->belongsTo(User::class); }
    public function fullText(): string { return "{$this->address_line1}, ".($this->address_line2 ? "{$this->address_line2}, " : "")."{$this->city}, {$this->state} - {$this->pincode}"; }
}
