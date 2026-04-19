<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\LoginService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private LoginService $loginService;

    private UserService $userService;

    public function __construct()
    {
        $this->loginService = new LoginService;
        $this->userService = new UserService;
    }

    public function index(Request $request)
    {

        $users = $this->userService->getUsers($request->all());

        return inertia('Dashboard/Personal', [
            'data' => $users,
            'filters' => [
                'search' => $request->input('search') ?? null,
            ],
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $randomPassword = bin2hex(random_bytes(8));
        $data['password'] = bcrypt($randomPassword);

        $user = $this->userService->createUser($data);

        $this->userService->sendPasswordSetupEmail($user);

        return inertia([
            'status' => true,
            'message' => 'Usuario creado exitosamente. Se ha enviado un correo para establecer la contraseña.',
            'data' => $user,
        ]);
    }

    public function show(int $id)
    {
        $user = $this->userService->getUserById($id);

        if (! $user) {
            return redirect()->back()->withErrors([
                'message' => 'El usuario solicitado no existe.'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'OK',
            'data' => $user,
        ], 200);
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $user = $this->userService->getUserById($id);

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user = $this->userService->updateUser($user, $data);

        return inertia('Dashboard/Personal', [
            'status' => true,
            'message' => 'Usuario actualizado exitosamente',
            'data' => $user,
        ]);
    }

    public function destroy(int $id)
    {
        $user = $this->userService->getUserById($id);

        if (! $user) {
            return redirect()->back()->withErrors([
                'message' => 'El usuario solicitado no existe.'
            ]);
        }

        $this->userService->deleteUser($user);

        return redirect('/dashboard/usuarios')->with([
            'status' => true,
            'message' => 'Usuario eliminado exitosamente',
        ]);
    }
}
