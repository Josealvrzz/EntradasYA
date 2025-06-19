<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CrudTestController extends Controller
{
    // Mostrar la lista de ítems
    public function index()
    {
        // Ejemplo de datos simulados
        $items = [
            (object)['id' => 1, 'nombre' => 'Ejemplo 1'],
            (object)['id' => 2, 'nombre' => 'Ejemplo 2'],
        ];
        return view('crud_test', compact('items'));
    }

    // Mostrar el formulario de creación
    public function create()
    {
        return view('crud_test_create');
    }

    // Guardar un nuevo ítem (simulado)
    public function store(Request $request)
    {
        // Aquí iría la lógica para guardar el ítem
        return redirect()->route('crud-test.index');
    }

    // Mostrar el formulario de edición
    public function edit($id)
    {
        // Ejemplo de ítem simulado
        $item = (object)['id' => $id, 'nombre' => 'Ejemplo ' . $id];
        return view('crud_test_edit', compact('item'));
    }

    // Actualizar un ítem (simulado)
    public function update(Request $request, $id)
    {
        // Aquí iría la lógica para actualizar el ítem
        return redirect()->route('crud-test.index');
    }

    // Eliminar un ítem (simulado)
    public function destroy($id)
    {
        // Aquí iría la lógica para eliminar el ítem
        return redirect()->route('crud-test.index');
    }
}