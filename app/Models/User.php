<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable {
    protected $fillable = ['name','username','email','password','role','is_active'];
    protected $hidden = ['password','remember_token'];
    protected function casts(): array { return ['password'=>'hashed', 'is_active'=>'boolean']; }
    public function canEdit(): bool { return in_array($this->role,['admin','petugas'],true); }
}
