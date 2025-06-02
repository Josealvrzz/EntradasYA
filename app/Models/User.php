<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'role_id'];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}