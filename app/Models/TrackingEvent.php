<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TrackingEvent extends Model {
    protected $fillable = ['shipment_id','status','description','location','event_at'];
    protected function casts(): array { return ['event_at'=>'datetime']; }
    public function shipment() { return $this->belongsTo(Shipment::class); }
}
