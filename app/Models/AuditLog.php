<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AuditLog extends Model {
    public $timestamps=false;
    protected $guarded=[];
    protected function casts(): array { return ['created_at'=>'datetime']; }
    public function user() { return $this->belongsTo(User::class); }
    public static function record(string $action,string $subject,?int $id=null): void {
        static::create(['user_id'=>auth()->id(),'action'=>$action,'subject'=>$subject,'subject_id'=>$id,'ip_address'=>request()->ip()]);
    }
}
