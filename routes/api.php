<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CrudTestController;
use Illuminate\Support\Facades\Validator;

// Rutas de autenticación

Route::post('/register', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6',
        'role_id' => 'required|exists:roles,id'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role_id' => $request->role_id
    ]);

    return response()->json($user, 201);
});


Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }
    $token = $user->createToken('auth_token')->plainTextToken;
    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user
    ]);
});

// Rutas CRUD
Route::apiResource('eventos', EventoController::class);
Route::apiResource('lugares', LugarController::class);
Route::apiResource('clientes', ClienteController::class);
Route::apiResource('entradas', EntradaController::class);
Route::apiResource('ventas', VentaController::class);
Route::apiResource('gastos', GastoController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('roles', RoleController::class);
Route::resource('crud-test', CrudTestController::class);