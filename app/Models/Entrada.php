<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    protected $fillable = ['evento_id', 'tipo', 'precio', 'cantidad_total', 'cantidad_disponible'];
    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}