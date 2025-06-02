<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = ['cliente_id', 'entrada_id', 'cantidad', 'total', 'estado'];
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function entrada()
    {
        return $this->belongsTo(Entrada::class);
    }
}