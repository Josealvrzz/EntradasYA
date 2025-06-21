<?php
namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Lugar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventoController extends Controller
{
    public function index(Request $request)
    {
        $query = Evento::with('lugar');

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where('nombre', 'like', "%$buscar%")
                  ->orWhereHas('lugar', function($q) use ($buscar) {
                      $q->where('nombre', 'like', "%$buscar%");
                  });
        }

     //   $eventos = $query->orderBy('fecha', 'desc')->paginate(10);
        $eventos = Evento::with('lugar')->orderBy('fecha', 'desc')->paginate(10);
        $totalEventos = Evento::count();
        $entradasVendidas = \App\Models\Entrada::count();
        $proximosEventos = Evento::where('fecha', '>=', Carbon::now())->count();

        return view('eventos.index', compact('eventos', 'totalEventos', 'entradasVendidas', 'proximosEventos'));
    }

    public function create()
{
    $lugares = \App\Models\Lugar::all();
    return view('eventos.create', compact('lugares'));
}

public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'fecha' => 'required|date',
        'lugar_id' => 'required|exists:lugares,id',
        'capacidad' => 'required|integer|min:1',
        'estado' => 'required|in:activo,finalizado,cancelado',
    ]);

    \App\Models\Evento::create($request->all());

    return redirect()->route('eventos.index')->with('success', 'Evento registrado correctamente.');
}

    public function show(Evento $evento)
    {
        return view('eventos.show', compact('evento'));
    }

    public function edit(Evento $evento)
    {
        $lugares = Lugar::all();
        return view('eventos.edit', compact('evento', 'lugares'));
    }

    public function update(Request $request, Evento $evento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'lugar_id' => 'required|exists:lugares,id',
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|in:activo,finalizado,cancelado',
        ]);

        $evento->update($request->all());

        return redirect()->route('eventos.index')->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(Evento $evento)
    {
        $evento->delete();
        return redirect()->route('eventos.index')->with('success', 'Evento eliminado correctamente.');
    }
}