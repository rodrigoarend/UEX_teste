<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteAccountReq;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function destroy(DeleteAccountReq $request)
    {
        $user = $request->user();

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Senha inválida.',
            ], 422);
        }

        DB::transaction(function () use ($user) {
            // contacts serão deletados via onDelete('cascade')
            $user->delete();
        });

        return response()->json([
            'message' => 'Conta excluída com sucesso.',
        ]);
    }
}
