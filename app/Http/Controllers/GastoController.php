<?php
namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;

class GastoController extends Controller
{
    public function index()
    {
        return Gasto::with('evento')->get();
    }

    public function store(Request $request)
    {
        $gasto = Gasto::create($request->all());
        return response()->json($gasto, 201);
    }

    public function show($id)
    {
        return Gasto::with('evento')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $gasto = Gasto::findOrFail($id);
        $gasto->update($request->all());
        return response()->json($gasto, 200);
    }

    public function destroy($id)
    {
        Gasto::destroy($id);
        return response()->json(null, 204);
    }
}