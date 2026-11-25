<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Service extends Model {
    const METHODS=['IUD','Implan','Suntik','Pil','Kondom','MOW','MOP'];
    const ACTIONS=['Pemasangan','Ulangan','Ganti cara','Pencabutan','Konseling','Medis operatif'];
    protected $fillable=['participant_id','service_date','method','action','facility_id','officer_id','notes','next_visit_date'];
    protected function casts(): array { return ['service_date'=>'date','next_visit_date'=>'date','notes'=>'encrypted','appointment_note'=>'encrypted']; }
    public function participant() { return $this->belongsTo(Participant::class); }
    public function facility() { return $this->belongsTo(Facility::class); }
    public function officer() { return $this->belongsTo(User::class,'officer_id'); }
}
