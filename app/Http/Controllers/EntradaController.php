<?php
namespace App\Http\Controllers;

use App\Models\Entrada;
use Illuminate\Http\Request;

class EntradaController extends Controller
{
    public function index()
    {
        return Entrada::with(['evento', 'ventas'])->get();
    }

    public function store(Request $request)
    {
        $entrada = Entrada::create($request->all());
        return response()->json($entrada, 201);
    }

    public function show($id)
    {
        return Entrada::with(['evento', 'ventas'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $entrada = Entrada::findOrFail($id);
        $entrada->update($request->all());
        return response()->json($entrada, 200);
    }

    public function destroy($id)
    {
        Entrada::destroy($id);
        return response()->json(null, 204);
    }
}