<?php
namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return Role::with('users')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:roles'
        ]);
        $role = Role::create($validated);
        return response()->json($role, 201);
    }

    public function show($id)
    {
        return Role::with('users')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre,'.$id
        ]);
        $role->update($validated);
        return response()->json($role, 200);
    }

    public function destroy($id)
    {
        Role::destroy($id);
        return response()->json(null, 204);
    }
}