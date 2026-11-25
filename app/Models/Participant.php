<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Participant extends Model {
    protected $fillable=['nik','name','birth_date','spouse_name','address','region_id','phone','children_count'];
    protected $hidden=['nik_hash'];
    protected function casts(): array { return ['nik'=>'encrypted','spouse_name'=>'encrypted','address'=>'encrypted','phone'=>'encrypted','birth_date'=>'date']; }
    protected static function booted(): void {
        static::saving(function ($p) { if ($p->isDirty('nik')) $p->nik_hash=static::nikHash($p->nik); });
    }
    public static function nikHash(string $nik): string { return hash_hmac('sha256',$nik,config('app.key')); }
    public function region() { return $this->belongsTo(Region::class); }
    public function services() { return $this->hasMany(Service::class); }
    public function latestService() { return $this->hasOne(Service::class)->ofMany(['service_date'=>'max','id'=>'max']); }
    public function firstService() { return $this->hasOne(Service::class)->ofMany(['service_date'=>'min','id'=>'min']); }
    public function getMaskedNikAttribute(): string { return substr($this->nik,0,4).' •••• •••• '.substr($this->nik,-4); }
}
