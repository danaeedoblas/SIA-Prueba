<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class Usuarios extends Controller
{
    public function listaUsuarios(Request $request)
    {
        // Obtener el ID del usuario que inició sesión
        $userId = Auth::id();

        // Obtener el término de búsqueda del formulario
        $searchTerm = $request->input('search');

        // Consulta de búsqueda con cláusula OR para buscar en múltiples columnas
        $users = User::where('id', '!=', $userId)
            ->where(function ($query) use ($searchTerm) {
                $query->where('id', 'like', "%$searchTerm%")
                    ->orWhere('name', 'like', "%$searchTerm%")
                    ->orWhere('email', 'like', "%$searchTerm%");
            })
            ->orderBy('id', 'desc')
            ->paginate(5);

        // Pasar la lista de usuarios a la vista
        return view('dashboard.usuarios.usuarios', compact('users'));
    }



    public function guardar(Request $request)
    {
        // Define los mensajes de error personalizados
        $messages = [
            'name.required' => 'El campo nombre es obligatorio.',
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'role.required' => 'El campo rol es obligatorio.',
        ];

        // Validar los datos del formulario con los mensajes personalizados
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
        ], $messages);

        // Verificar si el correo electrónico está vacío
        if (empty($request->input('email'))) {
            return back()->withInput()->withErrors(['email' => 'El correo electrónico es obligatorio.']);
        }

        // Verificar si el correo electrónico ya existe
        if (User::where('email', $request->input('email'))->exists()) {
            return back()->withInput()->withErrors(['email' => 'El correo electrónico ya está registrado.']);
        }

        // Crear un nuevo usuario
        $user = new User();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        // Determinar el estado
        $estado = $request->has('estado') ? 1 : 0;
        $user->password = Hash::make($request->input('password'));
        $user->role = $request->input('role');
        $user->status = $estado;
        $user->save();

        // Redirigir a alguna vista o ruta después de guardar el usuario
        return redirect()->route('usuarios.lista')->with('success', 'Usuario creado exitosamente');
    }



    public function toggleUsuario($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->status = !$user->status;
            $user->save();
        }
    }


    public function eliminarUsuario($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return redirect()->route('usuarios.lista')->with('success', 'Usuario eliminado correctamente.');
        } else {
            return redirect()->route('usuarios.lista')->with('error', 'No se pudo encontrar el usuario');
        }
    }




    public function editarUsuario($id)
    {
        // Obtener el usuario a editar por su ID
        $usuario = User::find($id);

        // Pasar el usuario a la vista del formulario de edición
        return view('dashboard.usuarios.editarUsuario', compact('usuario'));
    }


    public function update(Request $request, $id)
    {
        // Encuentra el usuario por su ID
        $usuario = User::findOrFail($id);

        // Actualiza los campos del usuario con los datos del formulario
        $usuario->name = $request->input('name');
        $usuario->email = $request->input('email');
        $usuario->role = $request->input('role');
        // Verifica si el checkbox de estado está marcado y actualiza el estado del usuario en consecuencia
        $usuario->status = $request->has('status') ? 1 : 0;

        // Verifica si se proporcionó una nueva contraseña
        if ($request->filled('password')) {
            $usuario->password = bcrypt($request->input('password'));
        }

        // Guarda los cambios en la base de datos
        $usuario->save();

        // Redirecciona a alguna página después de guardar los cambios
        return redirect()->route('usuarios.lista')->with('success', 'Usuario actualizado correctamente.');
    }


    public function profileUpdate(Request $request)
    {
        $user = Auth::user();

        // Validar los campos que pueden ser actualizados
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed', // La contraseña debe ser confirmada
        ]);

        // Actualizar los campos del usuario
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        // Si la contraseña no está en blanco, actualízala; de lo contrario, conserva la contraseña anterior
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Guardar los cambios en la base de datos
        $user->save();

        // Redireccionar a alguna página después de guardar los cambios
        return redirect()->route('login')->with('success', 'Perfil actualizado correctamente.');
    }
}
