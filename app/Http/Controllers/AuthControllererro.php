<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     * Cria um usuário e retorna token.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * POST /api/auth/login
     * Valida credenciais e retorna token.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas são inválidas.'],
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Opcional: revogar tokens antigos
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * POST /api/auth/logout
     * Revoga o token atual.
     */
    public function logout(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }

    /**
     * DELETE /api/auth/user
     * Confere a senha e, se bater, deleta o usuário e os contatos (em cascata).
     */
    public function destroy(Request $request)
    {
        $data = $request->validate([
            'password' => ['required'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        if (! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Senha inválida.'],
            ]);
        }

        // Apaga tokens do usuário
        $user->tokens()->delete();

        // Deleta usuário (e contatos por cascata)
        $user->delete();

        return response()->json([
            'message' => 'Conta excluída com sucesso.',
        ]);
    }

    /**
     * POST /api/auth/password/forgot
     * Envia e-mail com link de recuperação de senha.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Link de redefinição de senha enviado para o e-mail, se existir na base.',
            ]);
        }

        return response()->json([
            'message' => 'Não foi possível enviar o link de redefinição.',
        ], 400);
    }

    /**
     * POST /api/auth/password/reset
     * Redefine a senha usando token recebido por e-mail.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Opcional: revogar tokens antigos após reset
                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Senha redefinida com sucesso.',
            ]);
        }

        return response()->json([
            'message' => 'Não foi possível redefinir a senha.',
        ], 400);
    }
}
