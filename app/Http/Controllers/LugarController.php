<?php
namespace App\Http\Controllers;

use App\Models\Lugar;
use Illuminate\Http\Request;

class LugarController extends Controller
{
    public function index()
    {
        return Lugar::with('eventos')->get();
    }

    public function store(Request $request)
    {
        $lugar = Lugar::create($request->all());
        return response()->json($lugar, 201);
    }

    public function show($id)
    {
        return Lugar::with('eventos')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $lugar = Lugar::findOrFail($id);
        $lugar->update($request->all());
        return response()->json($lugar, 200);
    }

    public function destroy($id)
    {
        Lugar::destroy($id);
        return response()->json(null, 204);
    }
}