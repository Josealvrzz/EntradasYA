<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
    protected $table = 'lugares';
    protected $fillable = ['nombre', 'direccion', 'capacidad'];
    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }
}