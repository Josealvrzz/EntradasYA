<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = ['nombre', 'fecha', 'capacidad', 'lugar', 'descripcion'];
    public function lugar()
    {
        return $this->belongsTo(Lugar::class);
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class);
    }

    public function gastos()
    {
        return $this->hasMany(Gasto::class);
    }
}