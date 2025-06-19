<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Evento extends Model
{
   protected $fillable = ['nombre', 'fecha', 'lugar_id', 'capacidad', 'estado'];

    protected $casts = [
        'fecha' => 'datetime', // <-- Agrega esto
    ];

    public function lugar()
{
    return $this->belongsTo(\App\Models\Lugar::class, 'lugar_id');
}
    public function entradas()
    {
        return $this->hasMany(Entrada::class);
    }

    // Accesor para entradas vendidas
    public function getEntradasVendidasAttribute()
    {
        return $this->entradas()->count();
    }
}