<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Usuarios;
use App\Http\Middleware\CheckRole;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::view('/error/403', 'errors.403')->name('error.403');


Route::get('/', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');

Route::get('/profile', function () {
    return view('dashboard/profile');
})->name('profile');

Route::get('/usuarios', [Usuarios::class, 'listaUsuarios'])
    ->name('usuarios.lista')
    ->middleware('role:SuperAdmin,Administrador');

Route::get('/RegistrarUsuario', function () {
    return view('dashboard/alta-usuario');
})->name('registrar.usuario')->middleware('role:SuperAdmin,Administrador');

Route::post('/guardar-usuario', [Usuarios::class, 'guardar'])->name('guardar.usuario');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/solicitud', function () {
    return view('gastos/viaticos/solicitud');
})->name('solicitud');

Route::get('/comprobaciones', function () {
    return view('gastos/viaticos/comprobaciones');
})->name('comprobaciones');

Route::get('/comprobacion', function () {
    return view('gastos/viaticos/comprobacion');
})->name('comprobacion');

Route::get('/reposicion', function () {
    return view('gastos/viaticos/reposicion');
})->name('reposicion');
