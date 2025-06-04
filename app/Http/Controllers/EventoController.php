<?php
namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index()
    {
        return Evento::with('lugar')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'capacidad' => 'nullable|integer',
            'lugar' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
        ]);
        $evento = Evento::create($validated);
        return response()->json($evento, 201);
    }

    public function show($id)
    {
        return Evento::with('lugar')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'capacidad' => 'nullable|integer',
            'lugar' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
        ]);
        $evento->update($validated);
        return response()->json($evento);
    }

    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);
        $evento->delete();
        return response()->json(null, 204);
    }
}