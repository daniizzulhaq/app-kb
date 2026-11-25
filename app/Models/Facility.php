<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Facility extends Model {
    const TYPES=['Perawatan','PONEK','Poliklinik','Bedah'];
    protected $fillable=['name','region_id','type','address'];
    public function region() { return $this->belongsTo(Region::class); }
    public function services() { return $this->hasMany(Service::class); }
}
