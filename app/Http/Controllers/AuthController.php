<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SetupPasswordRequest;
use App\Models\PasswordSetupToken;
use App\Services\LoginService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    private LoginService $loginService;

    private UserService $userService;

    public function __construct()
    {
        $this->loginService = new LoginService;
        $this->userService = new UserService;
    }

    public function login(LoginRequest $request)
    {
        $dataUser = ['ci' => $request->ci, 'password' => $request->password];
        if (! $this->loginService->tryLoginOrFail($dataUser)) {
            return redirect('/')->withErrors(['data' => 'Datos incorrectos, intente nuevamente']);
        }

        $token = $this->loginService->generateToken($dataUser);
        $user = auth()->user();
        $permissionsArray = $this->userService->getPermissions($user->id);
        $permissionsWithFormat = $this->userService->formatToPermissions($permissionsArray);

        return Inertia::location('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function changePassword(Request $request)
    {
        $data = [
            'oldPassword' => $request->oldPassword,
            'newPassword' => $request->newPassword,
            'confirmPassword' => $request->confirmPassword,
        ];

        try {
            $this->loginService->tryChangePassword($data);

            return response()->json([
                'status' => true,
                'message' => 'Contraseña cambiada',
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function username()
    {
        return 'ci';
    }

    public function failLogin()
    {
        return 'No tiene los permisos para ingresar a esta url';
    }

    public function showSetupPassword(Request $request)
    {
        $token = $request->query('token');

        if (! $token) {
            return redirect('/')->with('error', 'Token no proporcionado');
        }

        $tokenRecord = PasswordSetupToken::where('token', $token)->first();

        if (! $tokenRecord || ! $tokenRecord->isValid()) {
            return redirect('/')->with('error', 'Token inválido o expirado');
        }

        return inertia('SetupPassword', ['token' => $token]);
    }

    public function setupPassword(SetupPasswordRequest $request)
    {
        $tokenRecord = PasswordSetupToken::where('token', $request->token)->first();

        if (! $tokenRecord) {
            return back()->with('error', 'Token inválido');
        }

        if (! $tokenRecord->isValid()) {
            return back()->with('error', 'El token ha expirado o ya fue utilizado');
        }

        $user = $tokenRecord->user;
        $user->password = bcrypt($request->password);
        $user->save();

        $tokenRecord->markAsUsed();

        return redirect('/')->with('success', 'Contraseña establecida exitosamente. Ahora puedes iniciar sesión.');
    }
}
