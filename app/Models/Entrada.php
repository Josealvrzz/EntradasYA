<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    protected $fillable = ['evento_id', 'usuario_id', 'codigo'];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}