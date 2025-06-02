<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $fillable = ['evento_id', 'concepto', 'monto', 'fecha'];
    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}