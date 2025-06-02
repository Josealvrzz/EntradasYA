<?php
namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function index()
    {
        return Venta::with(['cliente', 'entrada'])->get();
    }

    public function store(Request $request)
    {
        $venta = Venta::create($request->all());
        return response()->json($venta, 201);
    }

    public function show($id)
    {
        return Venta::with(['cliente', 'entrada'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $venta = Venta::findOrFail($id);
        $venta->update($request->all());
        return response()->json($venta, 200);
    }

    public function destroy($id)
    {
        Venta::destroy($id);
        return response()->json(null, 204);
    }
}