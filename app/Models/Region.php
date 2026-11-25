<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Region extends Model {
    protected $fillable=['name','code'];
    public function facilities() { return $this->hasMany(Facility::class); }
    public function participants() { return $this->hasMany(Participant::class); }
}
